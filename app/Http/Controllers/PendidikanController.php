<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendidikanController extends Controller
{
    public function index()
    {
        $data = DB::table('master_pendidikan')->get();

        return view('pendidikan.index', compact('data'));
    }

    public function create()
    {
        return view('pendidikan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat' => 'required|string|max:100',
        ]);

        DB::table('master_pendidikan')->insert([
            'tingkat' => $request->tingkat,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pendidikan.index')->with('success', 'Pendidikan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pendidikan = DB::table('master_pendidikan')->where('id_pendidikan', $id)->first();
        abort_if(! $pendidikan, 404);

        return view('pendidikan.edit', compact('pendidikan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tingkat' => 'required|string|max:100',
        ]);

        DB::table('master_pendidikan')->where('id_pendidikan', $id)->update([
            'tingkat' => $request->tingkat,
            'updated_at' => now(),
        ]);

        return redirect()->route('pendidikan.index')->with('success', 'Pendidikan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $dipakai = DB::table('pegawai')->where('id_pendidikan', $id)->exists();
        if ($dipakai) {
            return back()->with('error', 'Pendidikan tidak dapat dihapus karena masih digunakan pegawai');
        }

        DB::table('master_pendidikan')->where('id_pendidikan', $id)->delete();

        return back()->with('success', 'Pendidikan berhasil dihapus');
    }
}
