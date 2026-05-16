<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class TeacherSigninController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function create()
    {
        return view('teacher-signin');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => $request->boolean('confirm_other_device') ? 'nullable' : 'required',
        ]);

        $redirectUrl = $this->authService->attemptLogin($credentials, $request, ['teacher', 'admin']);

        if ($redirectUrl === 'confirm-other-device') {
            return back()
                ->withInput($request->only('email'))
                ->with('confirm_other_device', true);
        }

        if ($redirectUrl) {
            return redirect()->to($redirectUrl);
        }

        return back()->withErrors([
            'email' => 'Invalid email or password',
        ])->withInput();
    }
}
