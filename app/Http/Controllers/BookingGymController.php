<?php

namespace App\Http\Controllers;

use App\Models\BookingGym;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingGymController extends Controller
{
    public function store(Request $request)
	{
		$storeData = $request->all();

		$validate = Validator::make($storeData, [
			'id_member' => 'required',
			'tanggal' => 'required',
			'slot_waktu' => 'required',
		]);

		if ($validate->fails()) {
			return response(['message' => $validate->errors()], 400);
		}

		// Set the default status to 0
		$storeData['status'] = 0;

		// Check if member status is active
		$member = Member::find($storeData['id_member']);
		if (!$member || $member->status != 1) {
			return response(['message' => 'Member is not active'], 400);
		}

		// Check if booking gym for the date and slot_waktu has reached the capacity of 10
		$bookingsCount = BookingGym::where('tanggal', $storeData['tanggal'])
									->where('slot_waktu', $storeData['slot_waktu'])
									->count();
		if ($bookingsCount >= 10) {
			return response(['message' => 'Booking capacity reached limit'], 400);
		}

		// Check if member has already made a booking for the selected date
		$existingBooking = BookingGym::where('id_member', $storeData['id_member'])
									->where('tanggal', $storeData['tanggal'])
									->first();
		if ($existingBooking) {
			return response(['message' => 'Member has already made a booking for the selected date'], 400);
		}

		$bookingGym = BookingGym::create($storeData);

		return response([
			'message' => 'Add Booking Gym Success',
			'data' => $bookingGym
		], 200);
	}
}
