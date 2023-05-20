<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Member;
use App\Models\Instruktur;
use App\Models\Pegawai;

class AuthController extends Controller
{
	public function loginWeb(Request $request)
    {
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'username' => [
                'required',
                Rule::exists('pegawai')->where(function ($query) use ($loginData) {
                    $query->where('username', $loginData['username']);
                }),
            ],
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        if (!Auth::guard('pegawai')->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials'], 401);
        }

        $user = Auth::guard('pegawai')->user();

        return response([
            'message' => 'Authenticated',
            'data' => $user,
        ]);
    }


    public function loginAndroid(Request $request)
	{
		$loginData = $request->all();

		$validate = Validator::make($loginData, [
			'username' => [
				'required',
				function ($attribute, $value, $fail) use ($loginData) {
					$validMember = Member::where('username', $value)->exists();
					$validInstruktur = Instruktur::where('username', $value)->exists();
					$validPegawai = Pegawai::where('username', $value)->where('id_role', 2)->exists();

					if (!$validMember && !$validInstruktur && !$validPegawai) {
						$fail('Invalid Credentials');
					}
				},
			],
			'password' => 'required',
		]);

		if ($validate->fails()) {
			return response(['message' => $validate->errors()], 400);
		}

		$credentials = ['username' => $loginData['username'], 'password' => $loginData['password']];

		if (Auth::guard('member')->attempt($credentials)) {
			$user = Auth::guard('member')->user();
			return response([
				'message' => 'Authenticated as Member',
				'userType' => 'member',
				'data' => $user,
			]);
		} elseif (Auth::guard('instruktur')->attempt($credentials)) {
			$user = Auth::guard('instruktur')->user();
			return response([
				'message' => 'Authenticated as Instruktur',
				'userType' => 'instruktur',
				'data' => $user,
			]);
		} elseif (Auth::guard('pegawai')->attempt($credentials)) {
			$user = Auth::guard('pegawai')->user();
			return response([
				'message' => 'Authenticated as Pegawai',
				'userType' => 'pegawai',
				'data' => $user,
			]);
		} else {
			return response(['message' => 'Invalid Credentials'], 401);
		}
	}
}
