<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Aktivasi;
use App\Models\DepositKelas;
use Carbon\Carbon;
use Validator;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::all();

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

	public function show($id)
	{
		try {
			$member = Member::findOrFail($id);
			return response()->json(['data' => $member], 200);
		} catch (\Throwable $th) {
			return response()->json(['message' => 'Member not found.'], 404);
		}
	}

	public function store(Request $request)
	{
		$registrationData = $request->all();

		$validate = Validator::make($registrationData, [
			'nama_member' => 'required',
			'alamat' => 'required',
			'tanggal_lahir' => 'required',
			'telepon' => 'required',
			'email' => 'required',
			'username' => 'required',
			'password' => 'required',
		]);

		if ($validate->fails()) {
			return response(['message' => $validate->errors()], 400);
		}

		$currentDate = Carbon::now();
		$twoDigitYear = $currentDate->format('y');
		$twoDigitMonth = $currentDate->format('m');

		$latestIdMember = Member::max('id_member');
		$nextIdMember = ($latestIdMember ? $latestIdMember + 1 : 1);

		$idMember = $twoDigitYear . '.' . $twoDigitMonth . '.' . $nextIdMember;

		$registrationData['password'] = bcrypt($request->password);
		$registrationData['member_id'] = $idMember;
		$registrationData['tanggal_daftar'] = $currentDate;
		$registrationData['status'] = 0;
		$registrationData['sisa_deposit_reguler'] = 0;
		$registrationData['sisa_deposit_paket'] = 0;

		$user = Member::create($registrationData);

		return response([
			'message' => 'Register Success',
			'user' => $user
		], 200);
	}

	public function profileMember($id)
{
    $member = Member::findOrFail($id);

    $data = [
        'nama_member' => $member->nama_member,
        'alamat' => $member->alamat,
        'tanggal_lahir' => $member->tanggal_lahir,
        'telepon' => $member->telepon,
        'email' => $member->email,
        'sisa_deposit_reguler' => $member->sisa_deposit_reguler,
        'sisa_deposit_paket' => $member->sisa_deposit_paket,
    ];

    $aktivasi = Aktivasi::where('id_member', $id)->first();

    if ($aktivasi) {
        $data['masa_aktif'] = $aktivasi->masa_aktif;
    }

    // Join deposit_paket and kelas tables to get nama_kelas
    $depositPaket = DepositKelas::where('id_member', $id)
        ->join('kelas', 'deposit_paket.id_kelas', '=', 'kelas.id_kelas')
        ->select('kelas.nama_kelas')
        ->first();

    if ($depositPaket) {
        $data['nama_kelas'] = $depositPaket->nama_kelas;
    }

    return response()->json([
        'message' => 'Retrieve Member Success',
        'data' => $data
    ], 200);
}


}
