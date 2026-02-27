<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DinasLuarApiController extends Controller
{
    public function ajukan(Request $request)
    {
        $today = Carbon::today();

        // 1. Validasi Input Dasar
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = Auth::user();
        $start = Carbon::parse($request->tanggal_mulai);
        $end = Carbon::parse($request->tanggal_selesai);

        // [TAMBAHAN] VALIDASI TANGGAL LAMPAU
        if ($start->lt(Carbon::today())) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal! Tidak bisa mengajukan Dinas Luar untuk tanggal yang sudah lewat.',
            ], 422);
        }

        // ============================================================
        // [POIN 1] VALIDASI MAKSIMAL 3 HARI
        // ============================================================
        $durasi = $start->diffInDays($end) + 1; // Menghitung hari inklusif

        if ($durasi > 3) {
            return response()->json([
                'status' => false,
                'message' => "Gagal! Pengajuan Dinas Luar tidak boleh lebih dari 3 hari. Anda mencoba mengajukan $durasi hari.",
            ], 422);
        }

        // ============================================================
        // [POIN 5] CEK BENTROK JADWAL (ABSENSI & IZIN)
        // ============================================================
        $tempStart = $start->copy();

        while ($tempStart <= $end) {
            $tanggalCek = $tempStart->format('Y-m-d');

            // Cek apakah sudah ada di tabel absensi (Dinas Luar, Hadir, dll)
            $cekAbsensi = DB::table('absensi')
                ->where('id_pegawai', $user->id_pegawai)
                ->where('tanggal', $tanggalCek)
                ->exists();

            // Cek apakah sudah ada di tabel izin_pegawai (Sakit / Izin Keperluan)
            $cekIzin = DB::table('izin_pegawai')
                ->where('id_pegawai', $user->id_pegawai)
                ->where('tanggal', $tanggalCek)
                ->exists();

            if ($cekAbsensi || $cekIzin) {
                return response()->json([
                    'status' => false,
                    'message' => "Gagal! Jadwal bentrok. Anda sudah memiliki agenda (Absen/Izin/Dinas) pada tanggal $tanggalCek.",
                ], 422);
            }

            $tempStart->addDay();
        }

        // ============================================================
        // PROSES UPLOAD FOTO
        // ============================================================
        $namaFoto = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFoto = 'dinas_'.$user->id_pegawai.'_'.time().'.'.$file->getClientOriginalExtension();
            $tujuanPath = public_path('dinas_luar');

            if (! file_exists($tujuanPath)) {
                mkdir($tujuanPath, 0777, true);
            }
            $file->move($tujuanPath, $namaFoto);
        }

        try {
            return DB::transaction(function () use ($request, $user, $start, $end, $namaFoto) {
                // 2. Simpan ke tabel dinas_luar
                DB::table('dinas_luar')->insert([
                    'id_pegawai' => $user->id_pegawai,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'keterangan' => $request->keterangan,
                    'foto' => $namaFoto,
                    'status' => 'disetujui', // Sesuai enum di database: pending, disetujui, ditolak
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 3. Sinkronisasi ke tabel absensi harian
                $syncStart = $start->copy();
                while ($syncStart <= $end) {
                    DB::table('absensi')->updateOrInsert(
                        [
                            'id_pegawai' => $user->id_pegawai,
                            'tanggal' => $syncStart->format('Y-m-d'),
                        ],
                        [
                            'id_jenis' => 5, // ID Dinas Luar sesuai tabel jenis_potongan
                            'jam_masuk' => '08:00:00',
                            'jam_pulang' => '16:00:00',
                            'status_masuk' => 'Tanpa Keterangan',
                            'status_pulang' => 'Tanpa Keterangan',
                            'latitude' => 0,
                            'longitude' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $syncStart->addDay();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Dinas luar berhasil diajukan dan otomatis divalidasi ke sistem absensi.',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem: '.$e->getMessage(),
            ], 500);
        }
    }
}
