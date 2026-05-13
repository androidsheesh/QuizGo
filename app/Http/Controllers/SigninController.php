<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\User;

class SigninController extends Controller
{
    public function create()
    {
        return view('signin');
    }

    public function store(Request $request){
        $credentials =$request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        if (Auth::attempt($credentials)){
            $request->session()->regenerate();

            if(Auth::user()->role === 'teacher'){
                return redirect()->route('teacher.dashboard');
            } elseif(Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect('home');
        }

        return back()->withErrors([
            'email'=> 'Invalid email',

        ])->withInput();
    }
}
