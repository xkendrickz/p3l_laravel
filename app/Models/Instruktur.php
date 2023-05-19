<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruktur extends Model
{
    use HasFactory;

	protected $table = 'instruktur';
    protected $primaryKey = 'id_instruktur';
	/**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_instruktur ',
        'nama_instruktur',
        'tanggal_lahir',
		'jumlah_hadir ',
        'jumlah_libur',
        'waktu_terlambat',
		'username ',
        'password',
    ];
}
