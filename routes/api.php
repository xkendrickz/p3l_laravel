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
Route::apiResource('/instruktur',
App\Http\Controllers\InstrukturController::class);
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
Route::apiResource('/bookingGym',
App\Http\Controllers\BookingGymController::class);
Route::apiResource('/presensiInstruktur',
App\Http\Controllers\PresensiInstrukturController::class);

Route::delete('bookingGym/{id_member}/{tanggal}', 'App\Http\Controllers\BookingGymController@destroy');
Route::post('loginAndroid', 'App\Http\Controllers\AuthController@loginAndroid');
Route::post('loginWeb', 'App\Http\Controllers\AuthController@loginWeb');
Route::get('indexAktivasi', 'App\Http\Controllers\ResetController@indexAktivasi');
Route::get('indexDeposit', 'App\Http\Controllers\ResetController@indexDeposit');
Route::post('resetMember', 'App\Http\Controllers\ResetController@resetMember');
Route::post('resetInstruktur', 'App\Http\Controllers\ResetController@resetInstruktur');