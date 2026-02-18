<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LokasiKantorController extends Controller
{
    public function index()
    {
        $lokasi = DB::table('lokasi_kantor')->get();

        return view('lokasi.index', compact('lokasi'));
    }

    public function create()
    {
        return view('lokasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|integer',
        ]);

        DB::table('lokasi_kantor')->insert([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_master' => $request->radius,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function edit($id_lokasi)
    {
        $lokasi = DB::table('lokasi_kantor')
            ->where('id_lokasi', $id_lokasi)
            ->first();

        if (! $lokasi) {
            abort(404);
        }

        return view('lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, $id_lokasi)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|integer',
        ]);

        DB::table('lokasi_kantor')
            ->where('id_lokasi', $id_lokasi)
            ->update([
                'nama_lokasi' => $request->nama_lokasi,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_master' => $request->radius,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil diperbarui');
    }

    public function destroy($id_lokasi)
    {
        DB::table('lokasi_kantor')
            ->where('id_lokasi', $id_lokasi)
            ->delete();

        return redirect()
            ->route('lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus');
    }
}
