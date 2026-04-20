<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Ambil semua user beserta role-nya
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Ambil semua daftar role yang ada di database
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name'
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Berikan role kepada user tersebut
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Karyawan / User berhasil ditambahkan!');
    }

    public function destroy(User $user)
    {
        // Proteksi: Superadmin tidak boleh dihapus agar sistem tidak terkunci
        if ($user->hasRole('Superadmin')) {
            return back()->with('error', 'Akun Superadmin tidak boleh dihapus!');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}