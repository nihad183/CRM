<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $loginHistories = auth()->user()
            ->loginHistories()
            ->select(['id', 'user_id', 'login_at'])
            ->orderByDesc('login_at')
            ->limit(20)
            ->get();

        return view('pages.profil', compact('loginHistories'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'position' => $request->position,
        ]);

        return back()->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers()->mixedCase(),
            ],
        ]);

        $user->update([
            'password' => $request->password,
        ]);

        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Password updated successfully');
    }
}
