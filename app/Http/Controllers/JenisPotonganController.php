<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisPotonganController extends Controller
{
    public function index()
    {
        $data = DB::table('jenis_potongan')->get();

        return view('jenis_potongan.index', compact('data'));
    }

    public function create()
    {
        return view('jenis_potongan.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_potongan' => 'required|string|max:100',
            'nilai' => 'nullable|string|max:100',
        ]);

        DB::table('jenis_potongan')->insert([
            'nama_potongan' => $request->nama_potongan,
            'nilai' => $request->nilai,
        ]);

        return redirect()->route('jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $potongan = DB::table('jenis_potongan')
            ->where('id_jenis', $id)
            ->first();

        return view('jenis_potongan.form', compact('potongan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_potongan' => 'required|string|max:100',
            'nilai' => 'nullable|string|max:100',
        ]);

        DB::table('jenis_potongan')
            ->where('id_jenis', $id)
            ->update([
                'nama_potongan' => $request->nama_potongan,
                'nilai' => $request->nilai,
            ]);

        return redirect()->route('jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('jenis_potongan')
            ->where('id_jenis', $id)
            ->delete();

        return redirect()->route('jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil dihapus');
    }
}
