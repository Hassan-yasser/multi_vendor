<?php

namespace App\Http\Controllers;

use App\Contracts\Auth\AuthServiceContract;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceContract $auth,
    ) {}

    public function showLogin(): View
    {
        return $this->auth->showLogin();
    }

    public function showRegister(): View
    {
        return $this->auth->showRegister();
    }

    public function showForgotPassword(): View
    {
        return $this->auth->showForgotPassword();
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return $this->auth->showResetPassword($request, $token);
    }

    public function login(LoginRequest $request): JsonResponse|RedirectResponse
    {
        return $this->auth->respondToLogin($request);
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        return $this->auth->respondToLogout($request);
    }

    public function register(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        return $this->auth->respondToRegister($request);
    }

    public function sendResetLink(SendPasswordResetLinkRequest $request): JsonResponse|RedirectResponse
    {
        return $this->auth->respondToSendResetLink($request);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse|RedirectResponse
    {
        return $this->auth->respondToResetPassword($request);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->auth->respondToMe($request);
    }
}
