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

        // ============================================================
        // TAMBAHAN: CEK KUOTA DINAS LUAR (MAKSIMAL 3 PER HARI)
        // ============================================================
        $tempStart = $start->copy();
        while ($tempStart <= $end) {
            $tanggalCek = $tempStart->toDateString();

            // Hitung jumlah pegawai yang dinas luar di tanggal tersebut
            $countDinas = DB::table('dinas_luar')
                ->where('tanggal_mulai', '<=', $tanggalCek)
                ->where('tanggal_selesai', '>=', $tanggalCek)
                ->count();

            if ($countDinas >= 3) {
                return response()->json([
                    'status' => false,
                    'message' => "Gagal! Kuota Dinas Luar pada tanggal $tanggalCek sudah penuh (Maksimal 3 orang).",
                ], 422);
            }
            $tempStart->addDay();
        }
        // ============================================================

        try {
            return DB::transaction(function () use ($request, $user, $start, $end) {
                // 2. Simpan ke tabel dinas_luar
                DB::table('dinas_luar')->insert([
                    'id_pegawai' => $user->id_pegawai,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'keterangan' => $request->keterangan,
                    'status' => 'disetujui',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 3. Sinkronisasi ke tabel absensi harian
                while ($start <= $end) {
                    DB::table('absensi')->updateOrInsert(
                        [
                            'id_pegawai' => $user->id_pegawai,
                            'tanggal' => $start->format('Y-m-d'),
                        ],
                        [
                            'id_jenis' => 5, // ID untuk Dinas Luar
                            'status' => 'dinas_luar',
                            'jam_masuk' => '08:00:00',
                            'jam_pulang' => '16:00:00',
                            'latitude' => 0,
                            'longitude' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $start->addDay();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Dinas luar berhasil diajukan dan otomatis divalidasi',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }
}