<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
	public function register(Request $request){
        $registrationData = $request->all(); //mengambil seluruh data input dan menyimpan dalam variabel registrationData

        $validate = Validator::make($registrationData, [
            'username' => 'required',
            'password' => 'required',
            'email' => 'required',
            'tgglLahir' => 'required',
            'telepon' => 'required',

			// 'username' => 'required|max:60',
            // 'password' => 'required',
            // 'email' => 'required|email:rfc,dns|unique:users',
            // 'tgglLahir' => 'required',
            // 'telepon' => 'required',
        ]); //rule validasi input saat register

        if($validate->fails()) //mengecek apakah inputan sudah sesuai denga rule validasi
            return response(['message' => $validate->errors()],400); //mengembalikan error validasi input

        $registrationData['password'] = bcrypt($request->password); //untuk meng-enkripsi password

        $user = User::create($registrationData); //membuat user baru

        return response([
            'message' => 'Register Success',
            'data' => $user
        ],200); //return data user dalam bentuk json
    }

    public function login(Request $request){
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        if(!Auth::attempt($loginData))
        return response(['message' => 'Invalid Credentials'], 401); //mengembalikan error gagal login

        $user = Auth::user();
        // $token = $user->createToken('Authentication Token')->accessToken; //generate token

        return response([
            'message' => 'Authenticated',
            'data' => $user,
            // 'token_type' => 'Bearer',
            // 'access_token' => $token
        ]); //return data user dan token dalam bentuk json
    }
}
