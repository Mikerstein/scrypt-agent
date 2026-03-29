<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function show()
    {
        return view('password');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        Auth::user()->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()],
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors([
                'current_password' => 'Your current password is incorrect.'
            ]);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}