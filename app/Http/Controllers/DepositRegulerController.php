<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepositReguler;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepositRegulerController extends Controller
{
    public function store(Request $request)
	{
		$validatedData = $request->validate([
			'id_member' => 'required',
			'id_pegawai' => 'required',
			'deposit' => 'required',
		]);

		$id_member = $validatedData['id_member'];
		$id_pegawai = $validatedData['id_pegawai'];
		$deposit = $validatedData['deposit'];
		$bonus = 0;
		
		$member = Member::find($id_member);
		$sisa_deposit = $member->sisa_deposit;

		// Check if minimum sisa deposit is met and add bonus if applicable
		if ($sisa_deposit >= 500000 && $deposit >= 3000000) {
			$bonus = floor($deposit / 3000000) * 300000;
		}

		$latestDeposit = DB::table('deposit_reguler')->latest('id_deposit_reguler')->first();
		$latestIdDeposit = $latestDeposit ? $latestDeposit->id_deposit_reguler + 1 : 1;

		$now = Carbon::now();
		$no_struk = $now->format('y').".".$now->format('m').".".$latestIdDeposit;
		$tanggal = $now->format('Y-m-d');
		$total_deposit_reguler = $deposit + $bonus + $sisa_deposit;

		// Create new DepositReguler instance and save to database
		$depositReguler = new DepositReguler;
		$depositReguler->id_member = $id_member;
		$depositReguler->id_pegawai = $id_pegawai;
		$depositReguler->no_struk = $no_struk;
		$depositReguler->tanggal = $tanggal;
		$depositReguler->deposit = $deposit;
		$depositReguler->bonus = $bonus;
		$depositReguler->total_deposit_reguler = $total_deposit_reguler;
		$depositReguler->timestamps = false;
		$depositReguler->save();

		// Update member's sisa deposit
		$member->sisa_deposit = $total_deposit_reguler;
		$member->timestamps = false;
		$member->save();

		return response()->json([
			'message' => 'Deposit reguler has been stored successfully.'
		]);
	}

}
