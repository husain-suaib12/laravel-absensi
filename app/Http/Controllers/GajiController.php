<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GajiController extends Controller
{
    // MENAMPILKAN DAFTAR REKAP
    public function index(Request $request)
    {
        // Pastikan format bulan selalu 2 digit (01, 02, dst)
        $bulan = str_pad($request->input('bulan', date('m')), 2, '0', STR_PAD_LEFT);
        $tahun = $request->input('tahun', date('Y'));
        $periode = $tahun.'-'.$bulan;
        $user = Auth::user();

        // PERBAIKAN: Gunakan filter bulan agar data muncul sesuai pilihan
        $rekap = DB::table('rekap_gaji_bulanan')
            ->join('pegawai', 'rekap_gaji_bulanan.id_pegawai', '=', 'pegawai.id_pegawai')
            ->where('rekap_gaji_bulanan.bulan', $periode)

            ->select(
                'rekap_gaji_bulanan.*',
                'pegawai.nama',
                'pegawai.nik'
            )
            ->get();

        return view('gaji.index', compact('rekap', 'bulan', 'tahun'));
    }

    // GENERATE GAJI (DENGAN DETEKSI ERROR DETAIL)
    public function generate(Request $request)
    {
        try {
            $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
            $tahun = $request->tahun;
            $periode = $tahun.'-'.$bulan;

            $pegawaiList = DB::table('pegawai')
                ->join('users', 'users.id_pegawai', '=', 'pegawai.id_pegawai')
                ->join('absensi', 'pegawai.id_pegawai', '=', 'absensi.id_pegawai')
                ->join('jenis_potongan', 'absensi.id_jenis', '=', 'jenis_potongan.id_jenis')
                ->where('users.role', '=', 'pegawai')
                ->select('*'
                )
                ->get();
            foreach ($pegawaiList as $pegawai) {
                // Hitung Alfa (ID Jenis 1)
                $alfa = DB::table('absensi')
                    ->where('id_pegawai', $pegawai->id_pegawai)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('id_jenis', 1)->count();

                $gaji_pokok = $pegawai->gaji_pokok ?? 0;
                $total_potongan = $alfa * $pegawai->nilai;
                $gaji_bersih = $gaji_pokok - $total_potongan;

                // SIMPAN KE KEDUA NAMA KOLOM AGAR SINKRON WEB & ANDROID
                DB::table('rekap_gaji_bulanan')->updateOrInsert(
                    [
                        'id_pegawai' => $pegawai->id_pegawai,
                        'bulan' => $periode,
                    ],
                    [
                        'gaji_pokok' => $gaji_pokok, // WAJIB DISIMPAN
                        'jumlah_tanpa_keterangan' => $alfa,
                        'total_potongan' => $total_potongan,
                        'gaji_bersih' => $gaji_bersih,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

            }

            return response()->json(['status' => 'success', 'message' => 'Rekap Gaji Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // DETAIL GAJI
    public function detail(Request $request, $id_pegawai)
    {
        $bulan = str_pad($request->input('bulan', date('m')), 2, '0', STR_PAD_LEFT);
        $tahun = $request->input('tahun', date('Y'));
        $periode = $tahun.'-'.$bulan;

        $pegawai = DB::table('pegawai')->where('id_pegawai', $id_pegawai)->first();
        if (! $pegawai) {
            abort(404);
        }

        $rekap = DB::table('rekap_gaji_bulanan')->where('id_pegawai', $id_pegawai)->where('bulan', $periode)->first();

        $absensi = DB::table('absensi')
            ->join('jenis_potongan', 'absensi.id_jenis', '=', 'jenis_potongan.id_jenis')
            ->where('absensi.id_pegawai', $id_pegawai)
            ->whereYear('absensi.tanggal', $tahun)
            ->whereMonth('absensi.tanggal', $bulan)
            ->select('absensi.*', 'jenis_potongan.nama_potongan')
            ->get();

        return view('gaji.detail', compact('pegawai', 'rekap', 'absensi', 'bulan', 'tahun'));
    }
}
