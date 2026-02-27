<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminIzinApiController extends Controller
{
    public function izinPending(Request $request)
    {
        // Menampilkan semua data agar riwayat (termasuk yang ditolak) tetap terlihat
        $data = DB::table('izin_pegawai')
            ->join('pegawai', 'izin_pegawai.id_pegawai', '=', 'pegawai.id_pegawai')
            ->select('izin_pegawai.*', 'pegawai.nama as nama_pegawai')
            ->orderBy('izin_pegawai.id_izin', 'desc') // Perbaikan: Gunakan id_izin bukan created_at
            ->get();

        if (! $request->wantsJson()) {
            return view('izin.index', ['izin' => $data]);
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function validasiIzin(Request $request, $id_izin)
    {
        $request->validate([
            'status_izin' => 'required|in:disetujui,ditolak',
            'alasan_tolak' => 'required_if:status_izin,ditolak',
        ]);

        try {
            return DB::transaction(function () use ($request, $id_izin) {
                $izin = DB::table('izin_pegawai')->where('id_izin', $id_izin)->first();

                // 1. Update status di tabel izin_pegawai
                DB::table('izin_pegawai')->where('id_izin', $id_izin)->update([
                    'status_izin' => $request->status_izin,
                    'alasan_tolak' => $request->status_izin === 'ditolak' ? $request->alasan_tolak : null,
                ]);

                // 2. Logika Sinkronisasi ke Tabel Absensi
                // Jika DISETUJUI -> Masuk sebagai Izin/Sakit (Potongan 0 atau sesuai kebijakan)
                // Jika DITOLAK -> Masuk sebagai ALPA (id_jenis = 1) agar gaji terpotong

                $idJenis = ($request->status_izin === 'disetujui')
                            ? (strtolower(trim($izin->jenis)) === 'sakit' ? 2 : 3)
                            : 1; // Jika ditolak, otomatis jadi ALPA (id_jenis 1)

                $statusAbsensi = ($request->status_izin === 'disetujui')
                                ? (strtolower(trim($izin->jenis)) === 'sakit' ? 'sakit' : 'izin')
                                : 'alpa';

                DB::table('absensi')->updateOrInsert(
                    [
                        'id_pegawai' => $izin->id_pegawai,
                        'tanggal' => $izin->tanggal,
                    ],
                    [
                        'id_jenis' => $idJenis,
                        'id_jenis' => 3,
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'latitude' => 0,
                        'longitude' => 0,
                    ]
                );

                return redirect()->route('izin.index')->with('success', 'Status izin berhasil diperbarui dan disinkronkan ke absensi.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
