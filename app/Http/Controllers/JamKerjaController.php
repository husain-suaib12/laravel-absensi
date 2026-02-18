<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JamKerjaController extends Controller
{
    public function index()
    {
        // Biasanya jam kerja cuma 1
        $jamKerja = DB::table('jam_kerja')->first();

        return view('jam_kerja.index', compact('jamKerja'));
    }

    public function edit($id)
    {
        $jamKerja = DB::table('jam_kerja')
            ->where('id_jam', $id)
            ->first();

        if (! $jamKerja) {
            abort(404);
        }

        return view('jam_kerja.edit', compact('jamKerja'));
    }

    public function update(Request $request, $id)
    {
        // 🔒 Validasi format JAM 24 JAM (HH:MM)
        $request->validate([
            'jam_masuk_mulai' => ['required', 'date_format:H:i'],
            'jam_masuk_selesai' => ['required', 'date_format:H:i'],
            'jam_pulang_mulai' => ['required', 'date_format:H:i'],
            'jam_pulang_selesai' => ['required', 'date_format:H:i'],
        ]);

        DB::table('jam_kerja')
            ->where('id_jam', $id)
            ->update([
                'jam_masuk_mulai' => $request->jam_masuk_mulai,
                'jam_masuk_selesai' => $request->jam_masuk_selesai,
                'jam_pulang_mulai' => $request->jam_pulang_mulai,
                'jam_pulang_selesai' => $request->jam_pulang_selesai,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('jam-kerja.index')
            ->with('success', 'Jam kerja berhasil diperbarui (format 24 jam)');
    }
}
