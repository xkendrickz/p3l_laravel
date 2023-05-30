<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function historyMemberTransaksi($id)
{
    $aktivasi = DB::table('aktivasi')
        ->select('tanggal_aktivasi as tanggal', 'harga')
        ->where('id_member', $id)
        ->get();

    $depositKelas = DB::table('deposit_paket')
        ->join('kelas', 'deposit_paket.id_kelas', '=', 'kelas.id_kelas')
        ->select('deposit_paket.tanggal', 'deposit_paket.deposit', 'deposit_paket.harga', 'kelas.nama_kelas')
        ->where('id_member', $id)
        ->get();

    $depositReguler = DB::table('deposit_reguler')
        ->select('tanggal', 'deposit')
        ->where('id_member', $id)
        ->get();

    $data = [];

    if (!$aktivasi->isEmpty()) {
        foreach ($aktivasi as $akt) {
			$hargaFormatted = 'Rp.' . $akt->harga;
            $data[] = [
                'nama_aktivitas' => 'Aktivasi',
                'tanggal' => $akt->tanggal,
                'harga' => $hargaFormatted,
            ];
        }
    }

    if (!$depositKelas->isEmpty()) {
        foreach ($depositKelas as $depKelas) {
            $hargaFormatted = $depKelas->deposit . '(Rp.' . $depKelas->harga . ')';
            $data[] = [
                'nama_aktivitas' => 'Deposit Paket',
                'tanggal' => $depKelas->tanggal,
                'harga' => $hargaFormatted,
                'kelas' => $depKelas->nama_kelas,
            ];
        }
    }

    if (!$depositReguler->isEmpty()) {
        foreach ($depositReguler as $depReguler) {
			$hargaFormatted = 'Rp.' . $depReguler->deposit;
            $data[] = [
                'nama_aktivitas' => 'Deposit Reguler',
                'tanggal' => $depReguler->tanggal,
                'harga' => $hargaFormatted,
            ];
        }
    }

    // Sort the data by 'tanggal'
    $sortedData = collect($data)->sortByDesc('tanggal')->values()->all();

    $response = [
        'status' => 200,
        'error' => false,
        'message' => '',
        'data' => $sortedData,
    ];

    return response()->json($response, 200);
}

public function historyMemberPresensi($id)
{
    $gyms = DB::table('booking_gym')
        ->select('tanggal', 'slot_waktu', 'status')
        ->where('id_member', $id)
        ->get();

    $kelass = DB::table('booking_kelas')
        ->join('jadwal_harian', 'booking_kelas.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
		->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
		->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
        ->select('jadwal_harian.hari', 'booking_kelas.jenis', 'booking_kelas.status', 'kelas.nama_kelas')
        ->where('id_member', $id)
        ->get();

    $data = [];

    if (!$gyms->isEmpty()) {
        foreach ($gyms as $gym) {
            $statusText = $gym->status == 1 ? 'Hadir' : 'Tidak Hadir';
            $data[] = [
                'nama_aktivitas' => 'Gym',
                'tanggal' => $gym->tanggal,
                'jenis' => $gym->slot_waktu,
                'status' => $statusText,
            ];
        }
    }

    if (!$kelass->isEmpty()) {
        foreach ($kelass as $kelas) {
            $statusText = $kelas->status == 1 ? 'Hadir' : 'Tidak Hadir';
            $data[] = [
                'nama_aktivitas' => 'Kelas',
                'tanggal' => $kelas->hari,
                'jenis' => $kelas->jenis,
                'status' => $statusText,
                'kelas' => $kelas->nama_kelas,
            ];
        }
    }

    // Sort the data by 'tanggal'
    $sortedData = collect($data)->sortByDesc('tanggal')->values()->all();

    $response = [
        'status' => 200,
        'error' => false,
        'message' => '',
        'data' => $sortedData,
    ];

    return response()->json($response, 200);
}

public function historyInstruktur($id)
{
    $presensis = DB::table('presensi_instruktur')
		->join('jadwal_harian', 'presensi_instruktur.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
		->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
		->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
        ->select('kelas.nama_kelas', 'jadwal_harian.hari', 'presensi_instruktur.mulai_kelas', 'presensi_instruktur.selesai_kelas')
        ->where('id_instruktur', $id)
        ->get();

    $izins = DB::table('izin')
        ->join('jadwal_harian', 'izin.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
		->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
		->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
        ->select('kelas.nama_kelas', 'jadwal_harian.hari', 'izin.detail_izin')
        ->where('jadwal_umum.id_instruktur', $id)
        ->get();

    $data = [];

    if (!$presensis->isEmpty()) {
        foreach ($presensis as $presensi) {
            $data[] = [
                'nama_kelas' => $presensi->nama_kelas,
                'hari' => $presensi->hari,
                'mulai_kelas' => $presensi->mulai_kelas,
                'selesai_kelas' => $presensi->selesai_kelas,
            ];
        }
    }

    if (!$izins->isEmpty()) {
        foreach ($izins as $izin) {
            $data[] = [
                'nama_kelas' => $izin->nama_kelas,
                'hari' => $izin->hari,
                'izin' => $izin->detail_izin,
            ];
        }
    }

    // Sort the data by 'tanggal'
    $sortedData = collect($data)->sortByDesc('tanggal')->values()->all();

    $response = [
        'status' => 200,
        'error' => false,
        'message' => '',
        'data' => $sortedData,
    ];

    return response()->json($response, 200);
}


}
