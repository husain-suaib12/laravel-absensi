<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDepartemen extends Model
{
    use HasFactory;

    protected $table = 'master_departemen';
    protected $primaryKey = 'id_departemen';

    protected $fillable = ['nama_departemen'];
}