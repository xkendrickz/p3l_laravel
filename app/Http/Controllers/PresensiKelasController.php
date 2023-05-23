<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiKelasController extends Controller
{
    public function show($id_instruktur)
{
    $today = Carbon::now()->format('Y-m-d');

    $data = DB::table('booking_kelas')
        ->join('member', 'booking_kelas.id_member', '=', 'member.id_member')
        ->join('jadwal_harian', 'booking_kelas.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
        ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
        ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
        ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
        ->select(
            'kelas.nama_kelas',
            'instruktur.nama_instruktur',
            'member.nama_member',
			'booking_kelas.id_booking_kelas',
            'booking_kelas.jenis',
            'booking_kelas.status',
        )
        ->where('instruktur.id_instruktur', $id_instruktur)
        ->where('jadwal_harian.hari', $today)
        ->get();

    $response = [
        'message' => 'Data Kelas Berhasil Ditampilkan!',
        'data' => $data,
    ];

    return response()->json($response, 200);
}


}
