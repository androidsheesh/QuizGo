<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SigninController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function create()
    {
        return view('signin');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Controller handles HTTP Validation
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => $request->boolean('confirm_other_device') ? 'nullable' : 'required',
        ]);

        // 2. Ask the Service to attempt login and get the destination
        $redirectUrl = $this->authService->attemptLogin($credentials, $request, ['student']);

        // 3. If successful, regenerate session and redirect
        if ($redirectUrl) {
            if ($redirectUrl === 'confirm-other-device') {
                return back()
                    ->withInput($request->only('email'))
                    ->with('confirm_other_device', true);
            }

            return redirect()->to($redirectUrl);
        }

        // 4. If it fails, send them back with an error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email')); // Only send back the email, never the password
    }
}
