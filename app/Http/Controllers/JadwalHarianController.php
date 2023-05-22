<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalHarian;
use App\Models\JadwalUmum;
use Illuminate\Support\Facades\DB;
use DateTime;
use carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class JadwalHarianController extends Controller
{
    public function index()
	{
		$startDate = date('Y-m-d'); // Get current date
		$endDate = date('Y-m-d', strtotime('+7 days')); // Get date 7 days from now

		$data = DB::table('jadwal_harian')
			->select(
				'jadwal_harian.*',
				'jadwal_harian.hari',
				'jadwal_umum.jam',
				'kelas.nama_kelas',
				'instruktur.nama_instruktur',
				'kelas.tarif',
				DB::raw('IF(izin.konfirmasi = 1, izin.detail_izin, NULL) as status')
			)
			->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
			->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
			->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
			->leftJoin('izin', 'jadwal_harian.id_jadwal_harian', '=', 'izin.id_jadwal_harian')
			->whereBetween('jadwal_harian.hari', [$startDate, $endDate]) // Filter for the next 7 days
			->get();

		foreach ($data as &$row) {
			$row->nama_instruktur = $row->nama_instruktur;
			$row->nama_kelas = $row->nama_kelas;
			$row->hari = $row->hari;
			unset($row->id_instruktur, $row->id_kelas);
		}

		$response = [
			'status' => 200,
			'error' => false,
			'message' => '',
			'totaldata' => count($data),
			'data' => $data,
		];

		return response()->json($response, 200);
	}

	public function store()
	{
		$today = Carbon::today();

		$jadwalUmum = JadwalUmum::orderBy('hari')->get();

		foreach ($jadwalUmum as $jadwal) {
			$jadwalHarian = new JadwalHarian;

			$hari = Carbon::createFromFormat('Y-m-d', $jadwal->hari);
			$hariOfWeek = $hari->dayOfWeek;

			$nextDate = $today->copy()->addDays(($hariOfWeek - $today->dayOfWeek + 7) % 7);

			$jadwalHarian->hari = $nextDate->format('Y-m-d');
			$jadwalHarian->id_jadwal_umum = $jadwal->id_jadwal_umum;
			$jadwalHarian->timestamps = false;
			$jadwalHarian->save();
		}

		return response()->json(['message' => 'Jadwal Harian successfully generated.'], 200);
	}


	public function show($id){
        $data = JadwalHarian::findOrfail($id);
        $response = [
			'status' => 201,
			'error' => "false",
			'message' => "Register Berhasil",
			'data' => $data
		];
		return response()->json($response, 201);;
    }

	public function update(Request $request, JadwalHarian $jadwal_harian)
    {
        $validator = Validator::make($request->all(), [
			'status' => 'required'
		]);

		if ($validator->fails()){
			return response()->json($validator->errors(), 422);
		}
		
		return response()->json($jadwal_harian);
		$jadwal_harian = JadwalHarian::findOrFail($jadwal_harian->id_jadwal_harian);

		if($jadwal_harian) {
			$jadwal_harian->update([
				'status' => $request->status
			]);

			return response()->json([
				'message' => 'Status updated successfully.',
				'data' => $jadwal_harian
			]);
		}
    }
}
