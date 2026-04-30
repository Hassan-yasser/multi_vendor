<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthServiceContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Helper\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

final class AuthService implements AuthServiceContract
{
    public function __construct(
        private readonly AuthFactory $auth,
        private readonly UserRepositoryContract $users,
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function respondToLogin(LoginRequest $request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            if ($this->attemptLogin(
                $request,
                $request->validated('email'),
                $request->validated('password'),
                $request->remember(),
            )) {
                /** @var User $user */
                $user = Auth::user();

                return ApiResponse::success(
                    data: ['user' => $this->userPayload($user)],
                    message: __('Login successful.'),
                );
            }

            return ApiResponse::error(
                message: __('Invalid credentials.'),
                errors: ['email' => __('Invalid credentials.')],
                status: 401,
            );
        }

        if ($this->attemptLogin(
            $request,
            $request->validated('email'),
            $request->validated('password'),
            $request->remember(),
        )) {
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['email' => __('Invalid credentials.')])
            ->onlyInput('email');
    }

    public function respondToLogout(Request $request): JsonResponse|RedirectResponse
    {
        $this->performLogout($request);

        if ($request->wantsJson()) {
            return ApiResponse::success(
                data: null,
                message: __('Logout successful.'),
            );
        }

        return redirect()->route('login');
    }

    public function respondToRegister(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        $user = $this->registerAndLogin(
            $request,
            $data['name'],
            $data['email'],
            $data['password'],
        );

        if ($request->wantsJson()) {
            return ApiResponse::success(
                data: ['user' => $this->userPayload($user)],
                    message: __('Account created successfully.'),
                status: 201,
            );
        }

        return redirect()->route('dashboard');
    }

    public function respondToSendResetLink(SendPasswordResetLinkRequest $request): JsonResponse|RedirectResponse
    {
        $status = Password::sendResetLink(['email' => $request->validated('email')]);

        if ($request->wantsJson()) {
            if ($status === Password::RESET_LINK_SENT) {
                return ApiResponse::success(
                    data: null,
                    message: __($status),
                );
            }

            return ApiResponse::error(
                message: __($status),
                errors: ['email' => __($status)],
                status: 422,
            );
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function respondToResetPassword(ResetPasswordRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        $status = $this->brokerResetPassword(
            $data['email'],
            $data['password'],
            $data['password_confirmation'],
            $data['token'],
        );

        if ($request->wantsJson()) {
            if ($status === Password::PASSWORD_RESET) {
                return ApiResponse::success(
                    data: null,
                    message: __($status),
                );
            }

            return ApiResponse::error(
                message: __($status),
                errors: ['email' => __($status)],
                status: 422,
            );
        }

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }

    public function respondToMe(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success(
            data: ['user' => $this->userPayload($user)],
        );
    }

    public function showVerificationNotice(Request $request): View
    {
        return view('auth.verify-email');
    }

    public function sendVerificationEmail(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    public function verifyEmail(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::find($id);

        if (! $user || ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('verification.notice')->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $user->markEmailAsVerified();

        return redirect()->intended(route('dashboard'))->with('status', 'email-verified');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    private function attemptLogin(Request $request, string $email, string $password, bool $remember): bool
    {
        if (! $this->guard()->attempt(
            ['email' => $email, 'password' => $password],
            $remember,
        )) {
            return false;
        }

        $request->session()->regenerate();

        return true;
    }

    private function performLogout(Request $request): void
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function registerAndLogin(Request $request, string $name, string $email, string $password): User
    {
        $user = $this->users->createUser($name, $email, $password);
        $this->guard()->login($user);
        $request->session()->regenerate();

        return $user;
    }

    private function brokerResetPassword(
        string $email,
        string $password,
        string $passwordConfirmation,
        string $token,
    ): string {
        return Password::reset(
            [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
                'token' => $token,
            ],
            function (User $user, string $plainPassword): void {
                $this->users->persistPasswordAndRotateRememberToken($user, $plainPassword);
                event(new PasswordReset($user));
            }
        );
    }


    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
        ];
    }

    private function guard(): StatefulGuard
    {
        $guard = $this->auth->guard();

        if (! $guard instanceof StatefulGuard) {
            throw new \RuntimeException('The default auth guard must be stateful (session).');
        }

        return $guard;
    }
}
