<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'password' => 'required|string',
        ]);

        if ($data['password'] !== env('ADMIN_PASSWORD')) {
            return back()->withErrors([
                'password' => 'Senha inválida',
            ]);
        }

        session([
            'admin_authenticated' => true,
        ]);

        return redirect()->route('admin.participants');
    }

    public function logout()
    {
        session()->forget('admin_authenticated');

        return redirect()->route('admin.login');
    }
}
