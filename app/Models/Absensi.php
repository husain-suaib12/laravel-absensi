<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_pegawai',
        'id_jenis',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lat_masuk',
        'long_masuk',
        'lat_pulang',
        'long_pulang',
    ];

    public $timestamps = true;

    // Relasi ke pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
