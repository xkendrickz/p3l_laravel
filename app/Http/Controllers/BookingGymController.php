<?php

namespace App\Http\Controllers;

use App\Models\BookingGym;
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

        $bookingGym = BookingGym::create($storeData);

        return response([
            'message' => 'Add Booking Gym Success',
            'data' => $bookingGym
        ], 200);
    }
}
