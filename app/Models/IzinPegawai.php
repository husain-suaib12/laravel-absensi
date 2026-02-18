<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IzinPegawai extends Model
{
    protected $table = 'izin_pegawai';
    protected $primaryKey = 'id_izin';

    public $timestamps = false; 

    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'jenis',
        'keterangan',
        'foto',
        'status_izin',
    ];
}
