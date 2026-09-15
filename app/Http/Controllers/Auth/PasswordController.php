<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            // SECURITY FIX (AUTH-VULN-09): rotate the "remember me" token so
            // a previously-issued remember cookie (e.g. one that leaked, or
            // was left on a shared device) stops working once the password
            // is changed.
            'remember_token' => Str::random(60),
        ]);

        // SECURITY FIX (AUTH-VULN-06): changing the password used to leave
        // every other logged-in session (other browsers/devices, or an
        // attacker who already had a session) still valid. This invalidates
        // every other session's auth cookie while keeping the current one.
        Auth::logoutOtherDevices($validated['password']);

        return back()->with('status', 'password-updated');
    }
}
