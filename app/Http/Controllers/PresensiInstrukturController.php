<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
// use App\Models\PresensiInstruktur;
use Illuminate\Support\Facades\DB;

class PresensiInstrukturController extends Controller
{
    public function index()
	{
		$today = date('Y-m-d'); // Get current date

		$data = DB::table('instruktur')
			->select(
				'instruktur.id_instruktur',
				'instruktur.nama_instruktur',
				'kelas.id_kelas',
				'kelas.nama_kelas',
				'jadwal_harian.id_jadwal_harian'
			)
			->join('jadwal_umum', 'instruktur.id_instruktur', '=', 'jadwal_umum.id_instruktur')
			->join('jadwal_harian', 'jadwal_umum.id_jadwal_umum', '=', 'jadwal_harian.id_jadwal_umum')
			->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
			->where('jadwal_harian.hari', $today) // Filter for today's date
			->get();

		$response = [
			'message' => 'Data Kelas Berhasil Ditampilkan!',
			'data' => $data,
		];

		return response()->json($response, 200);
	}

	public function store(Request $request)
	{
		$validatedData = $request->validate([
			'id_jadwal_harian' => 'required',
			'mulai_kelas' => 'required',
			'selesai_kelas' => 'required',
		]);

		$presensiInstruktur = PresensiInstruktur::create($validatedData);

		return response([
			'message' => 'Presensi Instruktur created successfully',
			'data' => $presensiInstruktur,
		], 200);
	}

}
