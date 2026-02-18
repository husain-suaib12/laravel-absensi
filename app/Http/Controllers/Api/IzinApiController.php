<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterHariLibur;
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
            'jenis' => 'required|in:Izin,Sakit,izin,sakit',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $tanggalIzin = Carbon::parse($request->tanggal);

        // ===============================
        // 1. Tidak boleh tanggal lampau
        // ===============================
        if ($tanggalIzin->lt(Carbon::today())) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak bisa mengajukan izin untuk tanggal yang sudah lewat.',
            ], 422);
        }

        // ===============================
        // 2. Cek Hari Kerja (Weekend)
        // ===============================
        if ($tanggalIzin->isWeekend()) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal yang dipilih bukan hari kerja.',
            ], 422);
        }

        // ===============================
        // 3. Cek Hari Libur Nasional
        // ===============================
        $libur = MasterHariLibur::where('tanggal', $tanggalIzin->toDateString())
            ->where('is_active', 1)
            ->exists();

        if ($libur) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal yang dipilih adalah hari libur.',
            ], 422);
        }

        $user = Auth::user();

        // ===============================
        // 4. Cek Kuota Maksimal (5 Orang)
        // ===============================
        $jumlahPengajuIzin = DB::table('izin_pegawai')
            ->where('tanggal', $request->tanggal)
            ->count();

        if ($jumlahPengajuIzin >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'Kuota pengajuan izin untuk tanggal ini sudah penuh (Maksimal 5 orang).',
            ], 422);
        }

        // ===============================
        // 5. Cek Sudah Absensi
        // ===============================
        $cekAbsensi = DB::table('absensi')
            ->where('id_pegawai', $user->id_pegawai)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($cekAbsensi) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah tercatat melakukan absensi pada tanggal ini.',
            ], 422);
        }

        // ===============================
        // 6. Cek Izin Ganda
        // ===============================
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

        // ===============================
        // 7. Upload Foto (Jika Ada)
        // ===============================
        $namaFoto = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFoto = 'izin_'.$user->id_pegawai.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/izins', $namaFoto);
        }

        // ===============================
        // 8. Simpan ke Database
        // ===============================
        DB::table('izin_pegawai')->insert([
            'id_pegawai' => $user->id_pegawai,
            'tanggal' => $request->tanggal,
            'jenis' => ucfirst(strtolower($request->jenis)),
            'keterangan' => $request->keterangan,
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

    public function izinPending()
    {
        $data = DB::table('izin_pegawai')
            ->join('pegawai', 'izin_pegawai.id_pegawai', '=', 'pegawai.id_pegawai')
            ->select(
                'izin_pegawai.id_izin',
                'pegawai.nama as nama_pegawai',
                'izin_pegawai.tanggal',
                'izin_pegawai.jenis',
                'izin_pegawai.keterangan',
                'izin_pegawai.foto',
                'izin_pegawai.status_izin'
            )
            ->where('izin_pegawai.status_izin', 'pending')
            ->orderBy('izin_pegawai.tanggal', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function riwayatIzin()
    {
        $user = Auth::user();

        $izin = DB::table('izin_pegawai')
            ->select(
                'id_izin as id',
                'tanggal',
                'jenis',
                'keterangan',
                'status_izin as status',
                'alasan_tolak'
            )
            ->where('id_pegawai', $user->id_pegawai);

        $dinas = DB::table('dinas_luar')
            ->select(
                'id_dinas_luar as id',
                'tanggal_mulai as tanggal',
                DB::raw("'Dinas Luar' as jenis"),
                'keterangan',
                DB::raw("'disetujui' as status"),
                DB::raw('NULL as alasan_tolak')
            )
            ->where('id_pegawai', $user->id_pegawai);

        $riwayat = $izin->unionAll($dinas)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $riwayat,
        ]);
    }
}
