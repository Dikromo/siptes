<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function index()
    {
        return view('login', ['title' => 'Login', 'active' => 'login', 'active_sub' => '']);
    }
    public function auth(Request $request)
    {
        $validateData = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        $validateData['status'] = '1';
        if (Auth::attempt($validateData)) {
            $request->session()->regenerate();
            if (auth()->user()->roleuser_id === 1 || auth()->user()->roleuser_id === 2) {
                return redirect()->intended('/admin');
            } else {
                return redirect()->intended('/call');
            }
        }
        return back()->with('loginError', 'Login Failed');
    }
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    }
}
