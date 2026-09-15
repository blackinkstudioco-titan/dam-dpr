<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        //
        // SECURITY FIX (AUTH-VULN-04): the old code returned a different
        // response depending on whether Password::sendResetLink() found a
        // matching account (RESET_LINK_SENT vs. an "we can't find a user
        // with that email" error attached to the email field). That let
        // anyone enumerate every registered email address by trying them
        // here one at a time. We now always show the same generic status,
        // regardless of whether the email exists — only the account holder
        // who actually receives the email can tell the difference.
        Password::sendResetLink($request->only('email'));

        return back()->with('status', __('passwords.sent'));
    }
}
