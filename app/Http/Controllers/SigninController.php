<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return redirect('home');
        }

        return back()->withErrors([
            'email'=> 'Invalid email',

        ]);
    }
}
