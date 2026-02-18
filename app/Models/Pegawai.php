<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $primaryKey = 'id_pegawai';

    protected $fillable = [
        'nik',
        'nama',
        'id_jabatan',
        'id_pendidikan',
        'id_kantor',
        'gaji_pokok',
        'status_aktif',
        'no_hp',
        'alamat',
        'foto',
    ];

    /* ================== RELASI ================== */

    public function jabatan()
    {
        return $this->belongsTo(MasterJabatan::class, 'id_jabatan');
    }

    public function pendidikan()
    {
        return $this->belongsTo(MasterPendidikan::class, 'id_pendidikan');
    }

    public function kantor()
    {
        return $this->belongsTo(LokasiKantor::class, 'id_kantor', 'id_lokasi');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_pegawai', 'id_pegawai');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id_pegawai');
    }
}
