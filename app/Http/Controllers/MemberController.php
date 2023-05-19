<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
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
}
