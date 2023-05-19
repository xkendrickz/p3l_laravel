<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

	protected $table = 'member';
    protected $primaryKey = 'id_member';
	public $timestamps = false;
    /**
     * fillable
     * 
     * @var array
     */
    protected $fillable = [
        'id_member',
		'member_id',
		'nama_member',
		'alamat',
		'tanggal_lahir',
		'tanggal_daftar',
		'telepon',
		'email',
		'status',
		'sisa_deposit_reguler',
		'sisa_deposit_paket',
		'username',
		'password',
    ];

	/**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
