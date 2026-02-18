<?php

namespace App\Http\Controllers;

use App\Models\MasterJabatan;
use App\Models\MasterPendidikan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = DB::table('pegawai')
            ->join('master_jabatan', 'pegawai.id_jabatan', '=', 'master_jabatan.id_jabatan')
            ->join('master_pendidikan', 'pegawai.id_pendidikan', '=', 'master_pendidikan.id_pendidikan')

            ->select(
                'pegawai.*',
                'master_jabatan.nama_jabatan',
                'master_pendidikan.tingkat',

            )
            ->get();

        return view('pegawai.index', ['pegawai' => $pegawai]);
    }

    public function create()
    {
        $jabatan = MasterJabatan::all();
        $pendidikan = MasterPendidikan::all();

        return view('pegawai.create', compact('jabatan', 'pendidikan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'id_jabatan' => 'required',
            'id_pendidikan' => 'required',
            'status_aktif' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            'gaji_pokok' => 'required|numeric|min:0',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('foto'), $filename);
        } else {
            $filename = null;
        }

        DB::table('pegawai')->insert([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'id_jabatan' => $request->id_jabatan,
            'id_pendidikan' => $request->id_pendidikan,
            'status_aktif' => $request->status_aktif,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'gaji_pokok' => $request->gaji_pokok,
            'foto' => $filename,
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan!');
    }

    public function edit($id_pegawai)
    {
        $pegawai = DB::table('pegawai')->where('id_pegawai', $id_pegawai)->first();
        $jabatan = DB::table('master_jabatan')->get();
        $pendidikan = DB::table('master_pendidikan')->get();

        return view('pegawai.edit', compact('pegawai', 'jabatan', 'pendidikan'));
    }

    public function update(Request $request, $id_pegawai)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'id_jabatan' => 'required',
            'id_pendidikan' => 'required',
            'status_aktif' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            'gaji_pokok' => 'required|numeric|min:0',
            'foto' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $pegawai = DB::table('pegawai')->where('id_pegawai', $id_pegawai)->first();

        // Jika upload foto baru
        if ($request->hasFile('foto')) {

            if ($pegawai->foto && file_exists(public_path('foto/'.$pegawai->foto))) {
                unlink(public_path('foto/'.$pegawai->foto));
            }

            $file = $request->file('foto');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('foto'), $filename);

        } else {
            $filename = $pegawai->foto;
        }

        DB::table('pegawai')->where('id_pegawai', $id_pegawai)->update([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'id_jabatan' => $request->id_jabatan,
            'id_pendidikan' => $request->id_pendidikan,
            'status_aktif' => $request->status_aktif,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'gaji_pokok' => $request->gaji_pokok,
            'foto' => $filename,
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // 1. hapus user yang terhubung ke pegawai
            User::where('id_pegawai', $id)->delete();

            // 2. hapus pegawai
            Pegawai::where('id_pegawai', $id)->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pegawai dan user berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus pegawai',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
