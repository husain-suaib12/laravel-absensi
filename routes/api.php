<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Api\AbsenApiController;
use App\Http\Controllers\Api\AdminIzinApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DinasLuarApiController;
use App\Http\Controllers\Api\IzinApiController;
use App\Http\Controllers\Api\RekapGajiApiController;
use Illuminate\Support\Facades\Route;

Route::get('/lokasi', [AbsenApiController::class, 'getLokasi']);

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthApiController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED API
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
        |----------------------------------------------------------
        | PEGAWAI
        |----------------------------------------------------------
        */
    Route::middleware('role:pegawai')->group(function () {
        // Absensi & Status
        Route::post('/absen/masuk', [AbsenApiController::class, 'absenMasuk']);
        Route::post('/absen/pulang', [AbsenApiController::class, 'absenPulang']);
        Route::get('/status-absensi', [AbsenApiController::class, 'statusAbsensi']);
        Route::get('/riwayat-absensi', [AbsenApiController::class, 'riwayatAbsensi']);
        Route::post('/check-lokasi', [AbsenApiController::class, 'checkLokasi']);

        // Fitur Baru: Rekap Bulanan (Tabel & Gaji)
        Route::get('/rekap-bulanan', [AbsenApiController::class, 'getRekapBulanan']);

        // Izin & Dinas
        Route::post('/absensi', [AbsensiController::class, 'store']);
        Route::post('/izin', [IzinApiController::class, 'ajukanIzin']);
        Route::get('/riwayat-izin', [IzinApiController::class, 'riwayatIzin']); // Fitur Baru: Daftar Riwayat Izin
        Route::post('/dinas-luar', [DinasLuarApiController::class, 'ajukan']);
        Route::get('/pegawai/rekap-gaji', [RekapGajiApiController::class, 'rekapPegawai']);
        Route::post('/update-password', [AuthApiController::class, 'updatePassword']);
    });

    /*
    |----------------------------------------------------------
    | ADMIN
    |----------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/izin-pending', [AdminIzinApiController::class, 'izinPending']);
        Route::post('/admin/izin/{id_izin}/validasi', [AdminIzinApiController::class, 'validasiIzin']);
        Route::post('/admin/proses-rekap', [RekapGajiApiController::class, 'prosesRekap']);
    });

    // Logout
    Route::post('/logout', [AuthApiController::class, 'logout']);
});
