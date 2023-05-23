<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiGymController extends Controller
{
    public function index()
	{
		// Ambil data booking_gym dan join dengan tabel member berdasarkan id_member, kemudian diurutkan berdasarkan tanggal
		$data = DB::table('booking_gym')
					->join('member', 'booking_gym.id_member', '=', 'member.id_member')
					->select('booking_gym.id_booking_gym', 'member.nama_member', 'booking_gym.tanggal', 'booking_gym.slot_waktu', 'booking_gym.status')
					->orderBy('booking_gym.tanggal', 'asc')
					->get();

		$response = [
			'message' => 'Data Kelas Berhasil Ditampilkan!',
			'data' => $data,
		];

		return response()->json($response, 200);
	}

	public function update($id)
{
    // Update status to 1 in the booking_gym table
    DB::table('booking_gym')
        ->where('id_booking_gym', $id)
        ->update(['status' => 1]);

    // Generate no_struk
    $bookingData = DB::table('booking_gym')
        ->where('id_booking_gym', $id)
        ->select('id_booking_gym', 'tanggal')
        ->first();

    $tanggal = Carbon::parse($bookingData->tanggal);
    $no_struk = $tanggal->format('y.m') . '.' . $bookingData->id_booking_gym;

    // Store data in the presensi_gym table
    $now = Carbon::now()->format('Y-m-d H:i:s');
    $data = [
        'id_booking_gym' => $id,
        'no_struk' => $no_struk,
        'tanggal' => $now,
    ];
    DB::table('presensi_gym')->insert($data);

    // Get the inserted id_presensi_gym
    $id_presensi_gym = DB::getPdo()->lastInsertId();

    $response = [
        'message' => 'Status updated and data stored in presensi_gym table.',
        'data' => [
            'id_presensi_gym' => $id_presensi_gym,
            'no_struk' => $no_struk,
            'tanggal' => $now,
        ],
    ];

    return response()->json($response, 200);
}


public function show($id)
{
    try {
        // Get data from presensi_gym table based on the provided $id
        $data = DB::table('presensi_gym')
            ->join('booking_gym', 'presensi_gym.id_booking_gym', '=', 'booking_gym.id_booking_gym')
            ->join('member', 'booking_gym.id_member', '=', 'member.id_member')
            ->select(
                DB::raw("CONCAT(DATE_FORMAT(presensi_gym.tanggal, '%d/%m/%Y %H:%i')) AS tanggal"),
				'presensi_gym.no_struk',
                'member.member_id',
                'member.nama_member',
                'booking_gym.slot_waktu'
            )
            ->where('presensi_gym.id_presensi_gym', $id)
            ->first();

        if ($data) {
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['message' => 'Data not found.'], 404);
        }
    } catch (\Throwable $th) {
        return response()->json(['message' => 'An error occurred.'], 500);
    }
}
}
