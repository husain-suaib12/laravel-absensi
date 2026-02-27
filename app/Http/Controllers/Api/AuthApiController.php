<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (env('CHECK_FAKE_GPS') == true) {
            if ($request->is_mocked == true || $request->is_mocked == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Absensi Gagal! Terdeteksi penggunaan Fake GPS.',
                ], 403);
            }
        }

        // 🔥 PROSES LOGIN DULU
        if (! Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah',
            ], 401);
        }

        // 🔥 AMBIL USER YANG SUDAH LOGIN
        $user = Auth::user();
        $user->load(['pegawai.kantor']);

        $token = $user->createToken('android-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'status' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'role' => $user->role,
                'nama' => optional($user->pegawai)->nama ?? 'User Baru',
                'email' => $user->email,
                'username' => $user->username,
                'foto' => optional($user->pegawai)->foto
                    ? asset('foto/'.$user->pegawai->foto)
                    : null,
                'latitude' => optional($user->pegawai->kantor)->latitude ?? 0,
                'longitude' => optional($user->pegawai->kantor)->longitude ?? 0,
                'radius' => optional($user->pegawai->kantor)->radius_master ?? 100,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNGSI UBAH PASSWORD
    |--------------------------------------------------------------------------
    */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = Auth::user();

        // Verifikasi password lama sebelum update
        if (! Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password lama yang Anda masukkan salah',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diperbarui',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNGSI LOGOUT (MENGHILANGKAN ERROR MERAH VS CODE)
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        // Memberitahu VS Code tipe data token agar error 'delete' hilang
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}
