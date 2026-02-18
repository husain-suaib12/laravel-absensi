<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // ✅ WAJIB ADA
use App\Models\Absensi;
use App\Models\JenisPotongan;
use App\Models\Pegawai;
use App\Models\RekapGajiBulanan;
use Illuminate\Http\Request;

class RekapGajiApiController extends Controller
{
    public function prosesRekap(Request $request)
    {
        $bulan = $request->bulan; // format: 2026-02

        if (! $bulan) {
            return response()->json([
                'message' => 'Bulan wajib diisi format YYYY-MM',
            ], 400);
        }

        $pegawaiList = Pegawai::all();

        // Ambil potongan alfa sekali saja (lebih efisien)
        $potonganAlfa = JenisPotongan::where('id_jenis', 1)->first();
        $nilaiPotonganAlfa = $potonganAlfa ? (int) $potonganAlfa->nilai : 0;

        foreach ($pegawaiList as $pegawai) {

            $hadir = Absensi::where('id_pegawai', $pegawai->id_pegawai)
                ->where('id_jenis', 4)
                ->where('tanggal', 'like', $bulan.'%')
                ->count();

            $izin = Absensi::where('id_pegawai', $pegawai->id_pegawai)
                ->where('id_jenis', 3)
                ->where('tanggal', 'like', $bulan.'%')
                ->count();

            $sakit = Absensi::where('id_pegawai', $pegawai->id_pegawai)
                ->where('id_jenis', 2)
                ->where('tanggal', 'like', $bulan.'%')
                ->count();

            $alfa = Absensi::where('id_pegawai', $pegawai->id_pegawai)
                ->where('id_jenis', 1)
                ->where('tanggal', 'like', $bulan.'%')
                ->count();

            // Ambil gaji pokok dari tabel pegawai
            $gajiPokok = (int) $pegawai->gaji_pokok;

            // Hitung total potongan
            $totalPotongan = $alfa * $nilaiPotonganAlfa;

            // Hitung total gaji akhir
            $totalGaji = $gajiPokok - $totalPotongan;

            // Simpan / update rekap
            RekapGajiBulanan::updateOrCreate(
                [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'bulan' => $bulan,
                ],
                [
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alfa' => $alfa,
                    'total_gaji' => $totalGaji,
                ]
            );
        }

        return response()->json([
            'message' => 'Rekap gaji berhasil diproses',
        ]);
    }
}
