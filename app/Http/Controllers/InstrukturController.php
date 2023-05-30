<?php

namespace App\Http\Controllers;

/* Import Model */
use Mail;
use App\Mail\InstrukturMail;
use App\Models\Instruktur;
use Illuminate\Http\Request;
use App\Http\Resources\InstrukturResource;
use Illuminate\Support\Facades\Validator;

class InstrukturController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get instruktur
        $instruktur = Instruktur::latest()->get();

        //render view with posts
        return new InstrukturResource(true, 'List Data Instruktur', $instruktur);
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('instruktur.create');
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
	{
		// Validasi Formulir
		$validator = Validator::make($request->all(), [
			'nama_instruktur' => 'required',
			'tanggal_lahir' => 'required',
			'username' => 'required',
			'password' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		// Create new Instruktur record
		$instruktur = Instruktur::create([
			'nama_instruktur' => $request->nama_instruktur,
			'tanggal_lahir' => $request->tanggal_lahir,
			'jumlah_hadir' => 0,
			'jumlah_libur' => 0,
			'waktu_terlambat' => 0,
			'username' => $request->username,
			'password' => bcrypt($request->password) // Assuming you're storing hashed passwords
		]);

		return new InstrukturResource(true, 'Data Instruktur Berhasil Ditambahkan!', $instruktur);
	}

    public function destroy($id)
    {
        $instruktur = Instruktur::findOrfail($id);
        if($instruktur) {

            //delete post
            $instruktur->delete();
            return new InstrukturResource(true, 'Data Instruktur Berhasil Dihapus!', $instruktur);
        }
    }

    public function edit($id)
    {
        $instruktur=Instruktur::find($id);

        return view('instruktur.edit',compact('instruktur'));
    }

    public function update(Request $request, Instruktur $instruktur)
    {
        $validator = Validator::make($request->all(), [
            'nama_instruktur' => 'required',
            'tanggal_lahir' => 'required',
            'jumlah_pegawai' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $instruktur = Instruktur::findOrFail($instruktur->id);

        if($instruktur) {

            //update post
            $instruktur->update([
                'nama_instruktur'   => $request->nama_instruktur,
                'nama_manager'      => $request->nama_manager,
                'jumlah_pegawai'    => $request->jumlah_pegawai
            ]);

            return new InstrukturResource(true, 'Data Instruktur Berhasil Diubah!', $instruktur);
        }
    }

    public function show($id){
        $instruktur = Instruktur::findOrfail($id);
        return new InstrukturResource(true, 'Data Instruktur Berhasil Ditampilkan!', $instruktur);
    }

	public function profileInstruktur($id)
{
    $member = Instruktur::findOrFail($id);

    $data = [
        'nama_instruktur' => $member->nama_instruktur,
        'tanggal_lahir' => $member->tanggal_lahir	,
        'waktu_terlambat' => $member->waktu_terlambat,
    ];

    return response()->json([
        'message' => 'Retrieve Member Success',
        'data' => $data
    ], 200);
}
}