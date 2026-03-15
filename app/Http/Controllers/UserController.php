<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        // Proteksi manual di dalam fungsi biar gak bentrok sama logout
        if (auth()->user()->role !== 'admin') {
            return redirect('/dashboard');
        }

        return Inertia::render('Users/Index', [
            'users' => User::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin')
            return abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,staff',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'User berhasil ditambah!');
    }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,staff',
        ]);
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'Gak boleh nurunin pangkat diri sendiri bro!');
        }

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', "Role {$user->name} berhasil diubah jadi {$request->role}!");
    }
}