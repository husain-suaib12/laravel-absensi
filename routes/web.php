<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Api\AdminIzinApiController; // Pastikan ini terpanggil
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JamKerjaController;
use App\Http\Controllers\JenisPotonganController;
use App\Http\Controllers\LokasiKantorController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PendidikanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ===============================
// AUTH (PUBLIC)
// ===============================
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===============================
// PROTECTED ROUTES (HARUS LOGIN)
// ===============================
Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('welcome');

    // DATA PEGAWAI
    Route::resource('pegawai', PegawaiController::class);

    // MASTER DATA (LOKASI, JABATAN, PENDIDIKAN, JAM KERJA)
    Route::resource('lokasi', LokasiKantorController::class);
    Route::resource('jam-kerja', JamKerjaController::class)->except(['show', 'create', 'store']);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('pendidikan', PendidikanController::class);

    // ABSENSI
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/cetak', [AbsensiController::class, 'cetakPdf'])->name('absensi.cetak');

    // IZIN & SAKIT (SINKRON KE ABSENSI)
    Route::get('/izin', [AdminIzinApiController::class, 'izinPending'])->name('izin.index');
    Route::post('/izin/{id}/validasi', [AdminIzinApiController::class, 'validasiIzin'])->name('izin.validasi');

    // GAJI
    Route::prefix('gaji')->group(function () {

        Route::get('/', [GajiController::class, 'index'])->name('gaji.index');

        Route::get('/detail/{id_pegawai}', [GajiController::class, 'detail'])->name('gaji.detail');

        Route::get('/generate', [GajiController::class, 'generate'])->name('gaji.generate');
    });

    // MANAJEMEN USER & POTONGAN
    Route::resource('user', UserController::class);
    Route::resource('jenis-potongan', JenisPotonganController::class)->except(['show']);
});
//
