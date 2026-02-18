<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiKantor extends Model
{
    protected $table = 'lokasi_kantor';

    protected $primaryKey = 'id_lokasi';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius_master',

    ];
}
