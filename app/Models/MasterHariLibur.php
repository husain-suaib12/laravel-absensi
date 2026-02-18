<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterHariLibur extends Model
{
    protected $table = 'master_hari_libur';

    protected $primaryKey = 'id_libur';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'keterangan',
        'is_active',
    ];
}
