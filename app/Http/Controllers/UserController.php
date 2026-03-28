<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (auth()->user()->role !== 'admin') {
                    return redirect('/dashboard')->with('error', 'Akses ditolak! Khusus Admin.');
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
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

        return back()->with('success', 'User ' . $request->name . ' berhasil didaftarkan! 🚀');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,staff',
        ]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'Gak boleh nurunin pangkat sendiri bro, nanti lu gak bisa akses menu ini lagi!');
        }

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', 'Role ' . $user->name . ' berhasil diubah jadi ' . $request->role . ' ✅');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak boleh menghapus akun sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User ' . $user->name . ' berhasil dihapus dari sistem! 🗑️');
    }
}