<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPotongan extends Model
{
    protected $table = 'jenis_potongan';

    protected $primaryKey = 'id_jenis';

    public $timestamps = false;

    protected $fillable = [
        'nama_potongan',
        'nilai',
    ];
}
