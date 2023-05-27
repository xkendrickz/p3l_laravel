<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Aktivasi;
use App\Models\DepositKelas;
use App\Models\DepositReguler;
use App\Models\Kelas;
use App\Models\Instruktur;
use App\Models\PresensiInstruktur;
use App\Models\BookingGym;
use App\Models\BookingKelas;
use App\Models\Izin;
use App\Models\JadwalHarian;

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
				'nama_bulan' => Carbon::createFromDate($tahun, $month, 1)->format('F'),
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

	public function dropdownPendapatan()
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

	public function laporanAktivitasKelas($bulan, $tahun)
	{
		// Get the current date
		$currentDate = date('d F Y');

		$data = JadwalHarian::select('kelas.nama_kelas', 'instruktur.nama_instruktur')
			->addSelect(DB::raw('COUNT(booking_kelas.id_booking_kelas) as total_peserta'))
			->addSelect(DB::raw('COUNT(izin.id_izin) as total_libur'))
			->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
			->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
			->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
			->leftJoin('booking_kelas', 'jadwal_harian.id_jadwal_harian', '=', 'booking_kelas.id_jadwal_harian')
			->leftJoin('izin', 'jadwal_harian.id_jadwal_harian', '=', 'izin.id_jadwal_harian')
			->whereYear('jadwal_harian.hari', $tahun)
			->whereMonth('jadwal_harian.hari', $bulan)
			->groupBy('kelas.nama_kelas', 'instruktur.nama_instruktur')
			->orderBy('kelas.nama_kelas', 'asc')
			->get();

		// Prepare the response data including the total yearly amount
		$response = [
			'data' => $data,
			'bulan' => Carbon::createFromDate($bulan)->format('F'),
			'tahun' => $tahun,
			'tanggal' => $currentDate,
		];

		return response()->json($response, 200);
	}

	public function dropdownAktivitasKelas()
	{
		$months = JadwalHarian::selectRaw('MONTH(hari) as month')->distinct()->get();
		$years = JadwalHarian::selectRaw('YEAR(hari) as year')->distinct()->get();

		$response = [
			'data' => [
				'months' => $months,
				'years' => $years,
			],
		];

		return response()->json($response, 200);
	}

	public function laporanAktivitasGym($bulan, $tahun)
	{
		// Get the current date
		$currentDate = date('d F Y');

		// Get the data and total number of members for each date
		$data = BookingGym::select(DB::raw("DATE_FORMAT(tanggal, '%e %M %Y') as tanggal"), DB::raw('count(id_member) as jumlah_member'))
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->groupBy('tanggal')
        ->get();

		// Calculate the total number of members
		$total = $data->sum('jumlah_member');

		// Prepare the response data including the date, number of members, and total
		$response = [
			'data' => $data,
			'total' => $total,
			'bulan' => Carbon::createFromDate($bulan)->format('F'),
			'tahun' => $tahun,
			'tanggal' => $currentDate,
		];

		return response()->json($response, 200);
	}


	public function dropdownAktivitasGym()
	{
		$months = BookingGym::selectRaw('MONTH(tanggal) as month')->distinct()->get();
		$years = BookingGym::selectRaw('YEAR(tanggal) as year')->distinct()->get();

		$response = [
			'data' => [
				'months' => $months,
				'years' => $years,
			],
		];

		return response()->json($response, 200);
	}

	public function laporanKinerjaInstruktur($bulan, $tahun)
{
    // Get the current date
    $currentDate = date('d F Y');

    // Get the attendance count per instructor
    $data = Instruktur::select('instruktur.nama_instruktur')
        ->selectRaw('COUNT(presensi_instruktur.id_presensi_instruktur) as jumlah_hadir')
        ->selectRaw('COUNT(izin.id_izin) as jumlah_libur')
        ->selectRaw('SUM(instruktur.waktu_terlambat) as waktu_terlambat')
        ->leftJoin('jadwal_umum', 'instruktur.id_instruktur', '=', 'jadwal_umum.id_instruktur')
        ->leftJoin('jadwal_harian', 'jadwal_umum.id_jadwal_umum', '=', 'jadwal_harian.id_jadwal_umum')
        ->leftJoin('presensi_instruktur', 'jadwal_harian.id_jadwal_harian', '=', 'presensi_instruktur.id_jadwal_harian')
        ->leftJoin('izin', 'jadwal_harian.id_jadwal_harian', '=', 'izin.id_jadwal_harian')
        ->whereMonth('jadwal_harian.hari', $bulan)
        ->whereYear('jadwal_harian.hari', $tahun)
        ->groupBy('instruktur.id_instruktur', 'instruktur.nama_instruktur') // Include the column in the GROUP BY clause
        ->get();

    // Prepare the response data including the instructor name, attendance count, leave count, and late timing
    $response = [
        'data' => $data,
        'bulan' => Carbon::createFromDate($tahun, $bulan)->format('F'),
        'tahun' => $tahun,
        'tanggal' => $currentDate,
    ];

    return response()->json($response, 200);
}
}
