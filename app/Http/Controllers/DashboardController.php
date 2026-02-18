<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPegawai = \App\Models\Pegawai::count();

        // Hitung masing-masing status
        $hadir = DB::table('absensi')->where('id_jenis', 4)->count();
        $alpa = DB::table('absensi')->where('id_jenis', 1)->count();
        $izin = DB::table('absensi')->whereIn('id_jenis', [2, 3])->count();

        // Tambahkan ini untuk menghitung Dinas Luar secara spesifik
        $dinas = DB::table('absensi')->where('id_jenis', 5)->count();

        $absensiTerbaru = DB::table('absensi')
            ->join('pegawai', 'absensi.id_pegawai', '=', 'pegawai.id_pegawai')
            ->select('absensi.*', 'pegawai.nama')
            ->orderBy('absensi.tanggal', 'desc')
            ->limit(5)
            ->get();

        return view('welcome', compact('totalPegawai', 'hadir', 'alpa', 'izin', 'dinas', 'absensiTerbaru'));
    }
}
