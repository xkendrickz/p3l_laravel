<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::apiResource('/member',
App\Http\Controllers\MemberController::class);
Route::apiResource('/pegawai',
App\Http\Controllers\PegawaiController::class);
Route::apiResource('/kelas',
App\Http\Controllers\KelasController::class);
Route::apiResource('/izin',
App\Http\Controllers\IzinController::class);
Route::apiResource('/jadwalUmum',
App\Http\Controllers\JadwalUmumController::class);
Route::apiResource('/jadwalHarian',
App\Http\Controllers\JadwalHarianController::class);
Route::apiResource('/aktivasi',
App\Http\Controllers\AktivasiController::class);
Route::apiResource('/depositReguler',
App\Http\Controllers\DepositRegulerController::class);
Route::apiResource('/depositKelas',
App\Http\Controllers\DepositKelasController::class);

Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::get('indexAktivasi', 'App\Http\Controllers\ResetController@indexAktivasi');
Route::get('indexDeposit', 'App\Http\Controllers\ResetController@indexDeposit');
Route::post('resetMember', 'App\Http\Controllers\ResetController@resetMember');
Route::post('resetInstruktur', 'App\Http\Controllers\ResetController@resetInstruktur');