<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IzinApiController extends Controller
{
    public function ajukanIzin(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required',
            'alasan' => 'nullable|string',
            'foto' => 'required',
        ]);

        $tanggalIzin = Carbon::parse($request->tanggal);

        // 1. Cek Hari Kerja (Weekend)
        if ($tanggalIzin->isWeekend()) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal yang dipilih bukan hari kerja.',
            ], 422);
        }

        // ============================================================
        // [AKTIFKAN] VALIDASI TANGGAL LAMPAU
        // ============================================================
        if ($tanggalIzin->lt(Carbon::today())) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal! Tidak bisa mengajukan izin untuk tanggal yang sudah lewat.',
            ], 422);
        }

        $user = Auth::user();

        // ============================================================
        // [SINKRONISASI] CEK APAKAH SUDAH ADA DINAS LUAR (POIN 5)
        // ============================================================
        // Mengecek di tabel absensi apakah id_jenis = 5 (Dinas Luar)
        $cekDinasLuar = DB::table('absensi')
            ->where('id_pegawai', $user->id_pegawai)
            ->where('tanggal', $request->tanggal)
            ->where('id_jenis', 5)
            ->exists();

        if ($cekDinasLuar) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal! Anda sudah terdaftar Dinas Luar pada tanggal ini.',
            ], 422);
        }

        // 2. Cek Kuota Maksimal (3 Orang)
        $jumlahPengajuIzin = DB::table('izin_pegawai')
            ->where('tanggal', $request->tanggal)
            ->count();

        if ($jumlahPengajuIzin >= 3) {
            return response()->json([
                'status' => false,
                'message' => 'Kuota pengajuan izin untuk tanggal ini sudah penuh (Maksimal 3 orang).',
            ], 422);
        }

        // 3. Cek Sudah Absensi (Hadir Biasa)
        $cekAbsensi = DB::table('absensi')
            ->where('id_pegawai', $user->id_pegawai)
            ->where('tanggal', $request->tanggal)
            ->whereNull('id_jenis') // Jika id_jenis null berarti absen hadir biasa
            ->exists();

        if ($cekAbsensi) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah tercatat melakukan absensi pada tanggal ini.',
            ], 422);
        }

        // 4. Cek Izin Ganda
        $cekIzin = DB::table('izin_pegawai')
            ->where('id_pegawai', $user->id_pegawai)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($cekIzin) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah mengirim pengajuan izin untuk tanggal ini.',
            ], 422);
        }

        // --- Logika Upload Foto & Simpan Tetap Sama ---
        $namaFoto = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFoto = 'izin_'.$user->id_pegawai.'_'.time().'.'.$file->getClientOriginalExtension();
            $tujuanPath = public_path('izins');
            if (! file_exists($tujuanPath)) {
                mkdir($tujuanPath, 0777, true);
            }
            $file->move($tujuanPath, $namaFoto);
        }

        DB::table('izin_pegawai')->insert([
            'id_pegawai' => $user->id_pegawai,
            'tanggal' => $request->tanggal,
            'jenis' => ucfirst(strtolower($request->jenis)),
            'alasan' => $request->alasan,
            'foto' => $namaFoto,
            'status_izin' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Izin berhasil diajukan.',
        ], 201);
    }

    public function riwayatIzin(Request $request)
    {
        $idPegawai = $request->user()->id_pegawai;

        $izin = DB::table('izin_pegawai')
            ->where('id_pegawai', $idPegawai)
            ->select(
                'id_izin as id',
                'tanggal',
                'jenis as keterangan',
                'status_izin as status',
                DB::raw('NULL as alasan_tolak'),
                DB::raw("'IZIN' as tipe"),
                'created_at'
            )
            ->get();

        $dinas = DB::table('dinas_luar')
            ->where('id_pegawai', $idPegawai)
            ->select(
                'id_dinas_luar as id',
                'tanggal_mulai as tanggal',
                'tanggal_selesai as tanggal',
                'keterangan',
                'status',
                DB::raw('NULL as alasan_tolak'),
                DB::raw("'DINAS' as tipe"),
                'created_at'
            )
            ->get();

        $riwayat = $izin
            ->merge($dinas)
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'status' => true,
            'data' => $riwayat,
        ]);
    }

    // public function riwayatIzin()
    // {
    //     $user = Auth::user();

    //     $izin = DB::table('izin_pegawai')
    //         ->select(
    //             'id_izin as id',
    //             'tanggal',
    //             'jenis',
    //             'alasan',
    //             'status_izin as status',
    //             'alasan_tolak'
    //         )
    //         ->where('id_pegawai', $user->id_pegawai)->get();

    //     $dinas = DB::table('dinas_luar')
    //         ->select(
    //             'id_dinas_luar as id',
    //             'tanggal_mulai as tanggal',
    //             DB::raw("'Dinas Luar' as jenis"),
    //             'alasan',
    //             DB::raw("'disetujui' as status"),
    //             DB::raw('NULL as alasan_tolak')
    //         )
    //         ->where('id_pegawai', $user->id_pegawai);

    //     $riwayat = $izin->unionAll($dinas)
    //         ->orderBy('tanggal', 'desc')
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $izin,
    //     ]);
    // }

    // public function riwayatIzin(Request $request)
    // {
    //     $idPegawai = $request->user()->id_pegawai;

    //     $izin = DB::table('izin_pegawai')
    //         ->where('id_pegawai', $idPegawai)
    //         ->select(
    //             'id_izin as id',
    //             'tanggal_mulai as tanggal',
    //             'jenis_izin as keterangan',
    //             'status',
    //             'alasan_tolak',
    //             DB::raw("'IZIN' as tipe")
    //         );

    //     $dinas = DB::table('dinas_luar')
    //         ->where('id_pegawai', $idPegawai)
    //         ->select(
    //             'id_dinas as id',
    //             'tanggal as tanggal',
    //             DB::raw("'Dinas Luar' as keterangan"),
    //             'status',
    //             DB::raw('NULL as alasan_tolak'),
    //             DB::raw("'DINAS' as tipe")
    //         );

    //     $riwayat = $izin
    //         ->unionAll($dinas)
    //         ->orderBy('tanggal', 'desc')
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $riwayat,
    //     ]);
    // }
}
