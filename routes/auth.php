<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Public self-registration is intentionally disabled: this is an internal
// DPR RI system and accounts must be provisioned by an admin via the
// User management screen (see UserController, gated by role:admin).
// If self-service registration is ever required again, re-add the two
// routes below, but note that the User model's `role` is mass-assignable,
// so RegisteredUserController::store() must keep setting the role
// explicitly (never from request input) to avoid privilege escalation.
//
// Route::middleware('guest')->group(function () {
//     Route::get('register', [RegisteredUserController::class, 'create'])
//                 ->name('register');
//     Route::post('register', [RegisteredUserController::class, 'store']);
// });

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    // SECURITY FIX (AUTH-VULN-05): unauthenticated, previously-unthrottled
    // endpoints. Without a rate limit these can be hammered to mass-mail
    // reset links (abuse/spam vector) or brute-forced as part of the
    // account-enumeration timing side channel. 6 attempts/minute per
    // email+IP combination matches the throttle Laravel already applies to
    // the sibling verify-email/login-confirmation routes below.
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
