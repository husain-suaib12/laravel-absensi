<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProsesAbsensiHarian extends Command
{
    protected $signature = 'absensi:proses-harian';

    protected $description = 'Proses absensi harian otomatis (izin & alpa)';

    public function handle()
    {

        $now = Carbon::today();
        $tanggal = $now->toDateString();

        // 1. CEK HARI SABTU & MINGGU (WEEKEND)
        // Jika hari ini Sabtu (6) atau Minggu (0), maka hentikan proses
        if ($now->isWeekend()) {
            $this->info("Hari ini ($tanggal) adalah hari libur. Proses Alpa dibatalkan.");

            return;
        }

        // 2. CEK HARI LIBUR NASIONAL/DESA (Dari Tabel Master Hari Libur)
        $isHariLibur = DB::table('master_hari_libur')
            ->where('tanggal', $tanggal)
            ->exists();

        if ($isHariLibur) {
            $this->info("Hari ini ($tanggal) terdaftar sebagai Hari Libur di sistem. Proses Alpa dibatalkan.");

            return;
        }

        // --- Mulai Proses Pengecekan Pegawai ---
        $pegawaiList = DB::table('pegawai')->get();

        foreach ($pegawaiList as $pegawai) {
            // Cek apakah sudah ada absensi hari ini
            $sudahAbsen = DB::table('absensi')
                ->where('id_pegawai', $pegawai->id_pegawai)
                ->where('tanggal', $tanggal)
                ->exists();

            if ($sudahAbsen) {
                continue;
            }

            // Cek izin yang DISETUJUI
            $izin = DB::table('izin_pegawai')
                ->where('id_pegawai', $pegawai->id_pegawai)
                ->where('tanggal', $tanggal)
                ->where('status_izin', 'disetujui')
                ->first();

            // Tentukan id_jenis
            if ($izin) {
                $idJenis = ($izin->jenis === 'sakit') ? 2 : 3; // 2=Sakit, 3=Izin
            } else {
                $idJenis = 1; // 1=Alpa
            }

            // Insert ke tabel absensi
            DB::table('absensi')->insert([
                'id_pegawai' => $pegawai->id_pegawai,
                'tanggal' => $tanggal,
                'id_jenis' => $idJenis,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('Absensi harian (Alpa & Izin) berhasil diproses.');

    }
}
