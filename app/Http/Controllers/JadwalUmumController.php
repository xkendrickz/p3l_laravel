<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instruktur;
use Illuminate\Support\Facades\DB;

class JadwalUmumController extends Controller
{
    public function index()
	{
		$data = DB::table('jadwal_umum')
			->select('jadwal_umum.*', 'instruktur.nama_instruktur', 'kelas.nama_kelas', 'kelas.tarif')
			->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
			->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
			->orderBy('jadwal_umum.hari', 'asc')
			->get();

		foreach ($data as &$row) {
			$row->nama_instruktur = $row->nama_instruktur;
			$row->nama_kelas = $row->nama_kelas;
			$row->hari = $row->hari;
			unset($row->id_instruktur, $row->id_kelas);
		}

		$response = [
			'status' => 200,
			'error' => "false",
			'message' => '',
			'totaldata' => count($data),
			'data' => $data,
		];

		return response()->json($response, 200);
	}
}
