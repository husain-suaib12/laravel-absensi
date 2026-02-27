<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ===============================
    // TAMPILKAN HALAMAN LOGIN
    // ===============================
    public function showLogin()
    {
        return view('auth.login');
    }

    // ===============================
    // PROSES LOGIN (WEB)
    // ===============================
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            // CEK ROLE
            if (Auth::user()->role !== 'admin') {
                Auth::logout();

                return back()->withErrors([
                    'username' => 'Akses ditolak. Anda bukan admin.',
                ]);
            }

            return redirect()->route('welcome');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah',
        ])->withInput();
    }

    // ===============================
    // LOGOUT
    // ===============================
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
