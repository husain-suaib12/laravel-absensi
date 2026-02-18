<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('pegawai')->get();

        return view('user.index', compact('users'));
    }

    public function create()
    {
        // Ambil pegawai yang BELUM punya user
        $pegawai = Pegawai::whereDoesntHave('user')->get();

        return view('user.create', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pegawai' => 'required|unique:users,id_pegawai',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'id_pegawai' => $request->id_pegawai,
            'name' => Pegawai::find($request->id_pegawai)->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pegawai',
        ]);

        return redirect('/user')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        $data = [
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect('/user')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect('/user')->with('success', 'User berhasil dihapus');
    }
}
