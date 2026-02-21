<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    // ===============================
    // DETAIL ABSENSI
    // ===============================
    public function show($id_absensi)
    {
        $data = DB::table('absensi')
            ->join('pegawai', 'absensi.id_pegawai', '=', 'pegawai.id_pegawai')
            ->join('jenis_potongan', 'absensi.id_jenis', '=', 'jenis_potongan.id_jenis')

            ->select(
                'absensi.*',
                'pegawai.nama as nama_pegawai',
                'jenis_potongan.*'
            )
            ->where('absensi.id_absensi', $id_absensi)
            ->first();

        if (! $data) {
            abort(404, 'Data absensi tidak ditemukan');
        }

        return view('absensi.show', compact('data'));
    }

    // ===============================
    // CETAK PDF REKAP ABSENSI
    // ===============================
    public function cetakPdf(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $q = $request->q;

        // ✅ AMBIL LOKASI KANTOR
        $lokasi = DB::table('lokasi_kantor')->first();

        $query = DB::table('absensi')
            ->join('pegawai', 'absensi.id_pegawai', '=', 'pegawai.id_pegawai')
            ->leftJoin('jenis_potongan', 'absensi.id_jenis', '=', 'jenis_potongan.id_jenis')
            ->select(
                'absensi.*',
                'pegawai.nama as nama_pegawai');

        if ($q) {
            $query->where('pegawai.nama', 'like', "%{$q}%");
        }

        $absensi = $query
            ->whereMonth('absensi.tanggal', $bulan)
            ->whereYear('absensi.tanggal', $tahun)
            ->orderBy('absensi.tanggal', 'ASC')
            ->get();

        $pdf = Pdf::loadView('absensi.pdf', [
            'absensi' => $absensi,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'lokasi' => $lokasi, // ✅ INI KUNCINYA
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("Rekap-Absensi-$bulan-$tahun.pdf");
    }

    // ===============================
    // DATA ABSENSI (WEB ADMIN)
    // ===============================
    public function index(Request $request)
    {
        $bulan = $request->filled('bulan') ? $request->bulan : date('m');
        $tahun = $request->filled('tahun') ? $request->tahun : date('Y');
        $q = $request->get('q');

        // ✅ AMBIL LOKASI (UNTUK HEADER VIEW)
        $lokasi = DB::table('lokasi_kantor')->first();

        $query = DB::table('absensi')
            ->join('pegawai', 'absensi.id_pegawai', '=', 'pegawai.id_pegawai')
            ->leftJoin('jenis_potongan', 'absensi.id_jenis', '=', 'jenis_potongan.id_jenis')
            ->select(
                'absensi.*',
                'pegawai.nama as nama_pegawai',

            );

        if ($q) {
            $query->where('pegawai.nama', 'like', "%{$q}%");
        }

        $absensi = $query
            ->whereMonth('absensi.tanggal', $bulan)
            ->whereYear('absensi.tanggal', $tahun)
            ->orderBy('absensi.tanggal', 'desc')
            ->orderBy('absensi.jam_masuk', 'desc')
            ->paginate(25)
            ->appends($request->all());

        return view('absensi.index', compact(
            'absensi',
            'bulan',
            'tahun',
            'lokasi', //
            'q'
        ));
    }
}
