<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Aktivasi;
use App\Models\DepositKelas;
use App\Models\DepositReguler;

class LaporanController extends Controller
{
    public function laporanPendapatan($tahun)
	{
		// Get the current date
		$currentDate = date('d F Y');

		// Initialize an array to store the monthly data
		$monthlyData = [];

		// Initialize a variable to store the total yearly amount
		$totalTahunan = 0;

		// Iterate over each month
		for ($month = 1; $month <= 12; $month++) {
			// Calculate the total price from 'aktivasi' for the current month and year
			$totalAktivasi = Aktivasi::whereMonth('tanggal_aktivasi', $month)
				->whereYear('tanggal_aktivasi', $tahun)
				->sum('harga');

			// Calculate the total price from 'deposit_paket' for the current month and year
			$totalDepositPaket = DepositKelas::whereMonth('tanggal', $month)
				->whereYear('tanggal', $tahun)
				->sum('harga');

			// Calculate the total price from 'deposit_reguler' for the current month and year
			$totalDepositReguler = DepositReguler::whereMonth('tanggal', $month)
				->whereYear('tanggal', $tahun)
				->sum('deposit');

			// Calculate the total deposit (sum of 'deposit_paket' and 'deposit_reguler')
			$totalDeposit = $totalDepositPaket + $totalDepositReguler;

			// Calculate the total monthly amount (sum of 'aktivasi' and 'total_deposit')
			$totalBulanan = $totalAktivasi + $totalDeposit;

			// Prepare the monthly data
			$monthlyData[] = [
				'nama_bulan' => Carbon::createFromDate($month)->format('F'),
				'total_aktivasi' => $totalAktivasi,
				'total_deposit' => $totalDeposit,
				'total_bulanan' => $totalBulanan,
			];

			// Add the monthly amount to the total yearly amount
			$totalTahunan += $totalBulanan;
		}

		// Prepare the response data including the total yearly amount
		$response = [
			'data' => $monthlyData,
			'total_tahunan' => $totalTahunan,
			'tahun' => $tahun,
			'tanggal' => $currentDate
		];

		return response()->json($response, 200);
	}

	public function exposedDropdown()
	{
		$years = [];

		$yearsFromAktivasi = Aktivasi::pluck('tanggal_aktivasi')
			->map(function ($tanggal) {
				return Carbon::parse($tanggal)->format('Y');
			})
			->unique()
			->toArray();

		$yearsFromDepositReguler = DepositReguler::pluck('tanggal')
			->map(function ($tanggal) {
				return Carbon::parse($tanggal)->format('Y');
			})
			->unique()
			->toArray();

		$yearsFromDepositPaket = DepositKelas::pluck('tanggal')
			->map(function ($tanggal) {
				return Carbon::parse($tanggal)->format('Y');
			})
			->unique()
			->toArray();

		$years = array_merge($years, $yearsFromAktivasi, $yearsFromDepositReguler, $yearsFromDepositPaket);
		$years = array_unique($years);

		$response = [
			'data' => $years,
		];

		return response()->json($response, 200);
	}
}
