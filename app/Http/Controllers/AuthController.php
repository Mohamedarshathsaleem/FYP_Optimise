<?php


namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role; 

class AuthController extends Controller  // Extend Controller yang benar
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
         $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
    
        // ✅ Return errors + old input
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
        }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out!');
    }
    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
    
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',  // Default role untuk user dashboard
        ]);
    
      
        $role = Role::where('name', 'user')->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
            $user->update(['default_role_id' => $role->id]);
        }
    
       
        Auth::login($user);
    
        return redirect('/dashboard')->with('success', 'Registrasi berhasil dan login otomatis!');
    }

    public function showRegister()
    {
        return view('register');
    }
}
