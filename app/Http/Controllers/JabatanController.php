<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    public function index()
    {
        $data = DB::table('master_jabatan')->get();

        return view('jabatan.index', compact('data'));
    }

    public function create()
    {
        return view('jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
        ]);

        DB::table('master_jabatan')->insert([
            'nama_jabatan' => $request->nama_jabatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jabatan = DB::table('master_jabatan')->where('id_jabatan', $id)->first();
        abort_if(! $jabatan, 404);

        return view('jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
        ]);

        DB::table('master_jabatan')->where('id_jabatan', $id)->update([
            'nama_jabatan' => $request->nama_jabatan,
            'updated_at' => now(),
        ]);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $dipakai = DB::table('pegawai')->where('id_jabatan', $id)->exists();
        if ($dipakai) {
            return back()->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan pegawai');
        }

        DB::table('master_jabatan')->where('id_jabatan', $id)->delete();

        return back()->with('success', 'Jabatan berhasil dihapus');
    }
}
