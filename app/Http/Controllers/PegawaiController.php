<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Http\Resources\PegawaiResource;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    /**
    * index
    *
    * @return void
    */

    public function index()
    {
        $pegawai = Pegawai::where('id_role', 3)->get();
        return new PegawaiResource(true, 'List Data Pegawai', $pegawai);
    }
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        $departemen =  Departemen::all();
        return view('pegawai.create', compact('departemen'));
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'id_role' => 'required',
            'nama_pegawai' => 'required',
            'tanggal_lahir' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        //Fungsi Post ke Database
        $pegawai = Pegawai::create([
            'id_role' => $request->id_role,
            'nama_pegawai' => $request->nama_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'username' => $request->username,
            'password' => bcrypt($request->password)
        ]);

        return new PegawaiResource(true, 'Data Pegawai Berhasil Ditambahkan!', $pegawai);
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrfail($id);
        if($pegawai) {

            //delete post
            $pegawai->delete();
            return new PegawaiResource(true, 'Data Pegawai Berhasil Dihapus!', $pegawai);
        }
    }

    public function edit($id)
    {
        $pegawai=Pegawai::join('departemens','pegawais.id_departemen','=','departemens.id')->find($id);
        $departemen =  Departemen::all();
        return view('pegawai.edit',compact('pegawai','departemen'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validator = Validator::make($request->all(), [
            'nomor_induk_pegawai' => 'required',
            'nama_pegawai' => 'required',
            'id_departemen' => 'required',
            'email' => 'required',
            'telepon' => 'required',
            'gender' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $pegawai = Pegawai::findOrFail($pegawai->id);

        if($pegawai) {
            $pegawai->update([
                'nomor_induk_pegawai' => $request->nomor_induk_pegawai,
                'nama_pegawai' => $request->nama_pegawai,
                'id_departemen' => $request->id_departemen,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'gender' => $request->gender,
                'status' => $request->status
            ]);

            return new PegawaiResource(true, 'Data Pegawai Berhasil Diubah!', $pegawai);
        }
    }

    public function show($id){
        $pegawai = Pegawai::findOrfail($id);
        return new PegawaiResource(true, 'Data Pegawai Berhasil Ditampilkan!', $pegawai);
    }

	public function profilePegawai($id)
	{
		$member = Pegawai::findOrFail($id);

		$data = [
			'nama_pegawai' => $member->nama_pegawai,
			'tanggal_lahir' => $member->tanggal_lahir,
		];

		return response()->json([
			'message' => 'Retrieve Member Success',
			'data' => $data
		], 200);
	}
}
