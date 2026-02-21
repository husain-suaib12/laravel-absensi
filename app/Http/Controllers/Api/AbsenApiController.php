<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\MasterHariLibur;
use App\Models\Pegawai;
use App\Models\RekapGajiBulanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsenApiController extends Controller
{
    /**
     * ===============================
     * ABSEN MASUK
     * ===============================
     */
    public function absenMasuk(Request $request)
    {
        // $today = Carbon::today();

        // if ($today->isWeekend()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Hari ini bukan hari kerja.',
        //     ]);
        // }

        // $libur = MasterHariLibur::where('tanggal', $today->toDateString())
        //     ->where('is_active', 1)
        //     ->exists();

        // if ($libur) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Hari ini adalah hari libur.',
        //     ]);
        // }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_mocked' => 'required', // Wajib dikirim oleh Flutter
        ]);

        // LAYER KEAMANAN KEDUA: Cek flag is_mocked dari request
        if ($request->is_mocked == true || $request->is_mocked == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Absensi Gagal! Terdeteksi penggunaan Fake GPS.',
            ], 403);
        }

        $user = Auth::user();
        $tanggalHariIni = Carbon::now()->toDateString();

        // CEK HARI LIBUR
        // $hariLibur = DB::table('master_hari_libur')
        //     ->where('tanggal', $tanggalHariIni)
        //     ->where('is_active', 1)
        //     ->exists();

        // if ($hariLibur) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Hari ini adalah hari libur, tidak perlu absen',
        //     ], 403);
        // }

        $jamKerja = DB::table('jam_kerja')->first();
        if (! $jamKerja) {
            return response()->json(['status' => false, 'message' => 'Jam kerja belum diatur'], 500);
        }

        $jamSekarang = Carbon::now('Asia/Makassar')->format('H:i');

        if ($jamSekarang < $jamKerja->jam_masuk_mulai || $jamSekarang > $jamKerja->jam_masuk_selesai) {
            return response()->json([
                'status' => false,
                'message' => 'Absen masuk hanya dapat dilakukan pukul '.substr($jamKerja->jam_masuk_mulai, 0, 5).' - '.substr($jamKerja->jam_masuk_selesai, 0, 5),
            ], 403);
        }

        $pegawai = Pegawai::with('kantor')->where('id_pegawai', $user->id_pegawai)->first();
        if (! $pegawai || ! $pegawai->kantor) {
            return response()->json(['status' => false, 'message' => 'Data kantor tidak ditemukan'], 404);
        }

        $jarak = $this->hitungJarak($request->latitude, $request->longitude, $pegawai->kantor->latitude, $pegawai->kantor->longitude);

        if ($jarak > $pegawai->kantor->radius_master) {
            return response()->json(['status' => false, 'message' => 'Diluar radius kantor'], 403);
        }

        $cek = DB::table('absensi')->where('id_pegawai', $pegawai->id_pegawai)->where('tanggal', $tanggalHariIni)->first();
        if ($cek && $cek->id_jenis == 4) {
            return response()->json(['status' => false, 'message' => 'Anda sudah absen masuk'], 409);
        }

        DB::table('absensi')->updateOrInsert(
            ['id_pegawai' => $pegawai->id_pegawai, 'tanggal' => $tanggalHariIni],
            [
                'id_jenis' => 4,
                'jam_masuk' => Carbon::now()->format('H:i:s'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'id_jenis' => 4, // 4 = hadir
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['status' => true, 'message' => 'Absen masuk berhasil']);
    }

    /**
     * ===============================
     * ABSEN PULANG
     * ===============================
     */
    public function absenPulang(Request $request)
    {
        $today = Carbon::today();

        if ($today->isWeekend()) {
            return response()->json([
                'success' => false,
                'message' => 'Hari ini bukan hari kerja.',
            ]);
        }

        // $libur = MasterHariLibur::where('tanggal', $today->toDateString())
        //     ->where('is_active', 1)
        //     ->exists();

        // if ($libur) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Hari ini adalah hari libur.',
        //     ]);
        // }

        $request->validate(['latitude' => 'required|numeric', 'longitude' => 'required|numeric']);
        $user = Auth::user();
        $tanggal = Carbon::now()->toDateString();
        $jamSekarang = Carbon::now('Asia/Makassar')->format('H:i');
        $jamKerja = DB::table('jam_kerja')->first();

        if ($jamSekarang < $jamKerja->jam_pulang_mulai || $jamSekarang > $jamKerja->jam_pulang_selesai) {
            return response()->json(['status' => false, 'message' => 'Belum waktunya absen pulang'], 403);
        }

        $absen = DB::table('absensi')->where('id_pegawai', $user->id_pegawai)->where('tanggal', $tanggal)->first();
        if (! $absen) {
            return response()->json(['status' => false, 'message' => 'Belum absen masuk'], 404);
        }
        if ($absen->jam_pulang) {
            return response()->json(['status' => false, 'message' => 'Sudah absen pulang'], 409);
        }

        DB::table('absensi')->where('id_absensi', $absen->id_absensi)->update([
            'jam_pulang' => Carbon::now()->format('H:i:s'),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => true, 'message' => 'Absen pulang berhasil']);
    }

    public function statusAbsensi()
    {
        $today = Carbon::today();
        $tanggal = $today->toDateString();

        // 🔴 1. CEK SABTU & MINGGU
        // if ($today->isWeekend()) {
        //     return response()->json([
        //         'status' => true,
        //         'data' => [
        //             'status_absensi' => 'LIBUR',
        //             'keterangan' => 'Hari ini bukan hari kerja (Sabtu/Minggu)',
        //         ],
        //     ]);
        // }

        // // 🔴 2. CEK MASTER HARI LIBUR
        // $libur = MasterHariLibur::where('tanggal', $tanggal)
        //     ->where('is_active', 1)
        //     ->first();

        // if ($libur) {
        //     return response()->json([
        //         'status' => true,
        //         'data' => [
        //             'status_absensi' => 'LIBUR',
        //             'keterangan' => $libur->keterangan,
        //         ],
        //     ]);
        // }

        $user = Auth::user();

        if (! $user || ! $user->pegawai) {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai tidak ditemukan',
            ], 404);
        }

        $idPegawai = $user->pegawai->id_pegawai;
        $today = Carbon::today()->toDateString();

        // ===============================
        // 1️⃣ CEK IZIN HARI INI
        // ===============================
        $izin = DB::table('izin_pegawai')
            ->where('id_pegawai', $idPegawai)
            ->whereDate('tanggal', $today)
            ->first();

        if ($izin) {
            return response()->json([
                'status' => true,
                'data' => [
                    'status_absensi' => ($izin->status_izin === 'pending') ? 'Izin Pending' :
                        (($izin->status_izin === 'ditolak') ? 'Izin Ditolak' : strtoupper($izin->jenis)),
                    'alasan_tolak' => $izin->alasan_tolak,
                    'detail' => $izin->keterangan,
                ],
            ]);
        }

        // ===============================
        // 2️⃣ CEK ABSENSI HARI INI
        // ===============================
        $absensi = DB::table('absensi')
            ->where('id_pegawai', $idPegawai)
            ->whereDate('tanggal', $today)
            ->first();

        if ($absensi) {
            $statusTeks = $absensi->jam_pulang
                ? 'SUDAH ABSEN PULANG'
                : 'SUDAH ABSEN MASUK';

            return response()->json([
                'status' => true,
                'data' => [
                    'status_absensi' => $statusTeks,
                    'jam_masuk' => $absensi->jam_masuk,
                    'jam_pulang' => $absensi->jam_pulang,
                    'alasan_tolak' => null,
                ],
            ]);
        }

        // ===============================
        // 3️⃣ DEFAULT
        // ===============================
        return response()->json([
            'status' => true,
            'data' => [
                'status_absensi' => 'BELUM ABSEN',
                'alasan_tolak' => null,
            ],
        ]);
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * (2 * asin(sqrt($a)));
    }

    public function riwayatAbsensi()
    {
        $user = Auth::user();
        $riwayat = DB::table('absensi')
            ->where('id_pegawai', $user->id_pegawai)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $riwayat,
        ]);
    }

    /**
     * ===============================
     * AMBIL DATA LOKASI KANTOR (DIPANGGIL FLUTTER)
     * ===============================
     */
    public function getLokasi()
    {
        // SESUAI NAMA TABEL KAMU: lokasi_kantor
        $lokasi = DB::table('lokasi_kantor')->first();

        if (! $lokasi) {
            return response()->json([
                'status' => false,
                'message' => 'Data lokasi kantor belum diatur di Web Admin',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil data lokasi',
            'data' => [
                [
                    // Pastikan nama kolom di tabel kamu adalah latitude, longitude, dan radius
                    'latitude' => $lokasi->latitude,
                    'longitude' => $lokasi->longitude,
                    'radius_master' => $lokasi->radius_master,
                ],
            ],
        ]);
    }

    public function getRekapBulanan(Request $request)
    {
        $user = Auth::user();
        $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
        $tahun = $request->tahun;
        $periode = $tahun.'-'.$bulan;

        $absensi = DB::table('absensi')
            ->join('jenis_potongan', 'absensi.id_jenis', '=', 'jenis_potongan.id_jenis')
            ->where('absensi.id_pegawai', $user->id_pegawai)
            ->whereMonth('absensi.tanggal', $bulan)
            ->whereYear('absensi.tanggal', $tahun)
            ->select(
                'absensi.tanggal',
                'absensi.jam_masuk',
                // Gunakan COALESCE agar status tidak pernah NULL (Penyebab error Flutter)
                DB::raw("COALESCE(jenis_potongan.nama_potongan, '-') as status")
            )->get();

        $rekap = RekapGajiBulanan::where('id_pegawai', $user->id_pegawai)
            ->where('bulan', $periode)->first();

        return response()->json([
            'status' => true,
            'data' => [
                'absensi' => $absensi,
                'rekap_gaji' => [
                    'gaji_pokok' => (int) ($user->pegawai->gaji_pokok ?? 0),
                    'total_potongan' => $rekap ? (int) $rekap->total_potongan : 0,
                    'gaji_bersih' => $rekap ? (int) $rekap->gaji_bersih : (int) ($user->pegawai->gaji_pokok ?? 0),
                ],
            ],
        ]);
    }
}
