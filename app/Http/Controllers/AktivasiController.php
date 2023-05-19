<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aktivasi;
use Illuminate\Support\Facades\DB;

class AktivasiController extends Controller
{
	/**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
	{
		// get current year and month
		$currentYear = date('y');
		$currentMonth = date('m');

		// get latest id_aktivasi from database
		$latestAktivasi = DB::table('aktivasi')->latest('id_aktivasi')->first();
		$latestIdAktivasi = $latestAktivasi ? $latestAktivasi->id_aktivasi + 1 : 1;

		// get id_member and id_pegawai from request
		$idMember = $request->input('id_member');
		$idPegawai = $request->input('id_pegawai');

		// generate no_struk
		$noStruk = $currentYear . '.' . $currentMonth . '.' . $latestIdAktivasi;

		// get current date and time
		$currentDateTime = date('Y-m-d H:i:s');

		// calculate masa_aktif (1 year from currentDateTime)
		$masaAktif = date('Y-m-d', strtotime('+1 year', strtotime($currentDateTime)));

		// insert data to aktivasi table
		$aktivasi = DB::table('aktivasi')->insertGetId([
			'id_member' => $idMember,
			'id_pegawai' => $idPegawai,
			'no_struk' => $noStruk,
			'tanggal_aktivasi' => $currentDateTime,
			'harga' => 3000000,
			'masa_aktif' => $masaAktif
		]);

		// update member status
		DB::table('member')->where('id_member', $idMember)->update(['status' => true]);

		$response = [
			'status' => 201,
			'error' => false,
			'message' => 'Berhasil Transaksi Aktivasi',
			'data' => [
				'id_aktivasi' => $aktivasi
			]
		];

		return response()->json($response, 201);
	}

	public function show($id)
	{
		try {
			$aktivasi = Aktivasi::select('aktivasi.*', 'member.nama_member', 'member.member_id', 'pegawai.nama_pegawai', 'member.member_id')
				->join('member', 'aktivasi.id_member', '=', 'member.id_member')
				->join('pegawai', 'aktivasi.id_pegawai', '=', 'pegawai.id_pegawai')
				->findOrFail($id);
			return response()->json(['data' => $aktivasi], 200);
		} catch (\Throwable $th) {
			return response()->json(['message' => 'Aktivasi not found.'], 404);
		}
	}

}
