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
Route::apiResource('/presensiKelas',
App\Http\Controllers\PresensiKelasController::class);
Route::apiResource('/presensiGym',
App\Http\Controllers\PresensiGymController::class);

Route::get('historyInstruktur/{id}', 'App\Http\Controllers\HistoryController@historyInstruktur');
Route::get('historyMemberPresensi/{id}', 'App\Http\Controllers\HistoryController@historyMemberPresensi');
Route::get('historyMemberTransaksi/{id}', 'App\Http\Controllers\HistoryController@historyMemberTransaksi');
Route::get('profilePegawai/{id}', 'App\Http\Controllers\PegawaiController@profilePegawai');
Route::get('profileMember/{id}', 'App\Http\Controllers\MemberController@profileMember');
Route::get('profileInstruktur/{id}', 'App\Http\Controllers\InstrukturController@profileInstruktur');
Route::get('laporanKinerjaInstruktur/{bulan}/{tahun}', 'App\Http\Controllers\LaporanController@laporanKinerjaInstruktur');
Route::get('laporanAktivitasGym/{bulan}/{tahun}', 'App\Http\Controllers\LaporanController@laporanAktivitasGym');
Route::get('dropdownAktivitasGym', 'App\Http\Controllers\LaporanController@dropdownAktivitasGym');
Route::get('laporanAktivitasKelas/{bulan}/{tahun}', 'App\Http\Controllers\LaporanController@laporanAktivitasKelas');
Route::get('dropdownAktivitasKelas', 'App\Http\Controllers\LaporanController@dropdownAktivitasKelas');
Route::get('laporanPendapatan/{tahun}', 'App\Http\Controllers\LaporanController@laporanPendapatan');
Route::get('dropdownPendapatan', 'App\Http\Controllers\LaporanController@dropdownPendapatan');
Route::get('cetakStruk/{id}', 'App\Http\Controllers\PresensiKelasController@cetakStruk');
Route::delete('bookingGym/{id_member}/{tanggal}', 'App\Http\Controllers\BookingGymController@destroy');
Route::post('loginAndroid', 'App\Http\Controllers\AuthController@loginAndroid');
Route::post('loginWeb', 'App\Http\Controllers\AuthController@loginWeb');
Route::get('indexAktivasi', 'App\Http\Controllers\ResetController@indexAktivasi');
Route::get('indexDeposit', 'App\Http\Controllers\ResetController@indexDeposit');
Route::post('resetMember', 'App\Http\Controllers\ResetController@resetMember');
Route::post('resetInstruktur', 'App\Http\Controllers\ResetController@resetInstruktur');