<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

public function index()
{
$loginHistories = auth()->user()
    ->loginHistories()
    ->orderBy('login_at', 'desc')
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
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:6']
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Wrong password'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully');
    }
}