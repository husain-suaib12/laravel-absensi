<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapGajiBulanan extends Model
{
    protected $table = 'rekap_gaji_bulanan'; // Sesuaikan dengan nama tabel Anda

    // Daftarkan semua kolom agar bisa diisi oleh Controller
    protected $fillable = [
        'id_pegawai',
        'bulan',
        'alfa',
        'jumlah_tanpa_keterangan',
        'hadir',
        'izin',
        'sakit',
        'total_potongan',
        'gaji_bersih',
    ];
}
