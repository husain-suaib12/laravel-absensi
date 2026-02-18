<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapGajiController extends Controller
{
    public function generate(Request $request)
    {
        $bulan = $request->bulan; // format YYYY-MM
        if (! $bulan) {
            return back()->with('error', 'Bulan wajib dipilih');
        }

        [$tahun, $bulanAngka] = explode('-', $bulan);

        // ===============================
        // 1. HITUNG HARI KERJA (SENIN–JUMAT)
        // ===============================
        $start = Carbon::create($tahun, $bulanAngka, 1);
        $end = $start->copy()->endOfMonth();

        $hariKerja = 0;
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            if ($date->isWeekday()) {
                $hariKerja++;
            }
        }

        // ===============================
        // 2. KURANGI HARI LIBUR NASIONAL
        // ===============================
        $hariLibur = DB::table('master_hari_libur')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulanAngka)
            ->where('is_active', 1)
            ->count();

        $hariKerjaEfektif = max(0, $hariKerja - $hariLibur);

        // ===============================
        // 3. AMBIL POTONGAN ALPA (id_jenis = 1)
        // ===============================
        $potonganAlpa = DB::table('jenis_potongan')
            ->where('id_jenis', 1) // ALPA
            ->value('nominal') ?? 5000;

        DB::beginTransaction();

        try {
            $pegawaiList = DB::table('pegawai')->get();

            foreach ($pegawaiList as $p) {

                // ===============================
                // 4. HITUNG JUMLAH HADIR
                // ===============================
                $hadir = DB::table('absensi')
                    ->where('id_pegawai', $p->id_pegawai)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulanAngka)
                    ->count();

                // ===============================
                // 5. HITUNG ALPA
                // ===============================
                $alpa = $hariKerjaEfektif - $hadir;
                if ($alpa < 0) {
                    $alpa = 0;
                }

                // ===============================
                // 6. HITUNG TOTAL POTONGAN
                // ===============================
                $totalPotongan = $alpa * $potonganAlpa;

                // ===============================
                // 7. SIMPAN DETAIL POTONGAN (ALPA)
                // ===============================
                DB::table('potongan_gaji')->updateOrInsert(
                    [
                        'id_pegawai' => $p->id_pegawai,
                        'id_jenis' => 1, // ALPA
                        'bulan' => $bulan,
                    ],
                    [
                        'jumlah' => $alpa,
                        'total' => $totalPotongan,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                // ===============================
                // 8. HITUNG GAJI BERSIH
                // ===============================
                $gajiPokok = $p->gaji_pokok;
                $gajiBersih = $gajiPokok - $totalPotongan;

                // ===============================
                // 9. SIMPAN REKAP GAJI BULANAN
                // ===============================
                DB::table('rekap_gaji_bulanan')->updateOrInsert(
                    [
                        'id_pegawai' => $p->id_pegawai,
                        'bulan' => $bulan,
                    ],
                    [
                        'hari_kerja' => $hariKerjaEfektif,
                        'jumlah_hadir' => $hadir,
                        'jumlah_alpa' => $alpa,
                        'total_potongan' => $totalPotongan,
                        'gaji_bersih' => $gajiBersih,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            DB::commit();

            return back()->with('success', 'Rekap gaji berhasil digenerate');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
