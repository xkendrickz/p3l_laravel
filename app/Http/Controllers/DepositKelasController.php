<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\DepositKelas;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Kelas;

class DepositKelasController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'id_kelas' => 'required',
            'deposit' => 'required|in:5,10'
        ]);

        $idMember = $validatedData['id_member'];
        $idPegawai = $validatedData['id_pegawai'];
        $idKelas = $validatedData['id_kelas'];
        $deposit = $validatedData['deposit'];

        $kelas = DB::table('kelas')->where('id_kelas', $idKelas)->first();
        $tarif = $kelas->tarif;
        $harga = $tarif * $deposit;

        // check if member has already taken the maximum number of deposits for the class
        $depositCount = DB::table('deposit_paket')
            ->where('id_member', $idMember)
            ->where('id_kelas', $idKelas)
            ->where('jumlah_deposit_paket', '>', 0)
            ->count();

        if ($depositCount > 0) {
            return response()->json([
                'message' => 'Member has already taken the maximum number of deposits for this class'
            ], 400);
        }

        $jumlahDepositPaket = $deposit == 5 ? 6 : 13;
        $now = Carbon::now();
        $noStruk = $now->format('y').$now->format('m').$idKelas;

        // Calculate berlaku_sampai (1 month from tanggal)
        $berlaku_sampai = Carbon::parse($now)->addMonth()->addDays(1)->format('Y-m-d');

        // insert data to deposit_paket table
        $depositKelas = DB::table('deposit_paket')->insertGetId([
            'id_member' => $idMember,
            'id_pegawai' => $idPegawai,
            'id_kelas' => $idKelas,
            'no_struk' => $noStruk,
            'tanggal' => $now,
            'deposit' => $deposit,
            'harga' => $harga,
            'jumlah_deposit_paket' => $jumlahDepositPaket,
            'berlaku_sampai' => $berlaku_sampai
        ]);

        $response = [
            'status' => 201,
            'error' => false,
            'message' => 'Deposit kelas has been stored successfully.',
            'data' => [
                'id_deposit_paket' => $depositKelas
            ]
        ];

        return response()->json($response, 201);
    }

	public function show($id)
	{
		try {
			$aktivasi = DepositKelas::select('aktivasi.*', 'member.nama_member', 'pegawai.nama_pegawai', 'member.member_id', 'kelas.nama_kelas')
				->join('member', 'aktivasi.id_member', '=', 'member.id_member')
				->join('pegawai', 'aktivasi.id_pegawai', '=', 'pegawai.id_pegawai')
				->join('kelas', 'aktivasi.id_kelas', '=', 'kelas.id_kelas')
				->findOrFail($id);
			return response()->json(['data' => $aktivasi], 200);
		} catch (\Throwable $th) {
			return response()->json(['message' => 'Aktivasi not found.'], 404);
		}
	}
}
