<?php

namespace App\Contracts\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface AuthServiceContract
{
    public function showLogin(): View;

    public function showRegister(): View;

    public function showForgotPassword(): View;

    public function showResetPassword(Request $request, string $token): View;

    public function respondToLogin(LoginRequest $request): JsonResponse|RedirectResponse;

    public function respondToLogout(Request $request): JsonResponse|RedirectResponse;

    public function respondToRegister(RegisterRequest $request): JsonResponse|RedirectResponse;

    public function respondToSendResetLink(SendPasswordResetLinkRequest $request): JsonResponse|RedirectResponse;

    public function respondToResetPassword(ResetPasswordRequest $request): JsonResponse|RedirectResponse;

    public function respondToMe(Request $request): JsonResponse;

    public function showVerificationNotice(Request $request): View;

    public function sendVerificationEmail(Request $request): RedirectResponse;

    public function verifyEmail(Request $request, $id, $hash): RedirectResponse;

    public function updatePassword(Request $request): RedirectResponse;
}
