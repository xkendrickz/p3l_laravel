<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;

class IzinController extends Controller
{
    public function index()
    {
		$izin = Izin::select('izin.*', 'instruktur.nama_instruktur')
            ->join('instruktur', 'izin.id_instruktur', '=', 'instruktur.id_instruktur')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $izin,
        ]);
    }

	public function update(Request $request, $id)
	{
		$izin = Izin::find($id);

		if (!$izin) {
			return response()->json(['message' => 'Entitas Izin tidak ditemukan'], 404);
		}

		$izin->konfirmasi = 1;
		$izin->timestamps = false;
		$izin->save();

		return response()->json(['message' => 'Konfirmasi berhasil diperbarui']);
	}
}
