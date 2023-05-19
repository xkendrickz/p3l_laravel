<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Instruktur;
use App\Models\Aktivasi;
use App\Models\DepositKelas;
use Carbon\Carbon;

class ResetController extends Controller
{
    public function indexAktivasi()
	{
		$today = Carbon::today();

		// Members with aktivasi
		$activatedMembers = Member::join('aktivasi', 'member.id_member', '=', 'aktivasi.id_member')
			->where('member.status', 1)
			->whereDate('aktivasi.masa_aktif', '<=', $today)
			->select('member.nama_member AS memberAktivasi', 'aktivasi.masa_aktif')
			->get();

		return response()->json([
			'success' => true,
			'data' => $activatedMembers,
		]);
	}

	public function indexDeposit()
	{
		$today = Carbon::today();

		// Members with deposit paket
		$depositMembers = Member::join('deposit_paket', 'member.id_member', '=', 'deposit_paket.id_member')
			->where('member.sisa_deposit_paket', '>', 0)
			->whereDate('deposit_paket.berlaku_sampai', '<=', $today)
			->select('member.nama_member AS memberDeposit', 'deposit_paket.berlaku_sampai')
			->get();

		return response()->json([
			'success' => true,
			'data' => $depositMembers,
		]);
	}

	public function resetMember()
	{
		$currentDate = Carbon::now()->toDateString();

		// Update status member
		$members1 = Member::where('status', 1)->get();
		foreach ($members1 as $member) {
			$activation = Aktivasi::where('id_member', $member->id_member)->latest('tanggal_aktivasi')->first();
			if ($activation && $activation->masa_aktif <= $currentDate) {
				$member->status = 0;
				$member->timestamps = false;
				$member->save();
			}
		}

		$members2 = Member::where('sisa_deposit_paket','>', 0)->get();
		foreach ($members2 as $member) {
			$deposits = DepositKelas::where('id_member', $member->id_member)->latest('berlaku_sampai')->first();
			if ($deposits && $deposits->belaku_sampai <= $currentDate) {
				$member->sisa_deposit_paket = 0;
				$member->timestamps = false;
				$member->save();
			}
		}

		return response()->json(['message' => 'Member reset successfully']);
	}

	public function resetInstruktur()
	{
		$instrukturs = Instruktur::all();

		foreach ($instrukturs as $instruktur) {
			$instruktur->waktu_terlambat = 0;
			$instruktur->timestamps = false;
			$instruktur->save();
		}

		return response()->json(['message' => 'Instrukturs reset successfully']);
	}
}