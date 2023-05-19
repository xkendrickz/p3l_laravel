<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_pegawai',
        'id_role',
        'nama_pegawai',
        'tanggal_lahir',
        'username',
        'password'
    ];


}
