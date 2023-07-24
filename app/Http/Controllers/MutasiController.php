<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreMutasiRequest;
use App\Http\Requests\UpdateMutasiRequest;
use App\Models\Mutasi_list;

class MutasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view(
            'admin.pages.mutasi.index',
            [
                'title' => 'Mutasi',
                'active' => 'mutasi',
                'active_sub' => '',
                'data' => ''
            ]
        );
    }
    public function dataTables()
    {
        $data = Mutasi::latest();
        // switch (auth()->user()->roleuser_id) {
        //     case '1':
        //         $data = $data->latest();
        //         break;
        //     default:
        //         $data = $data->where('roleuser_id', '3')
        //             ->where('parentuser_id', auth()->user()->id)
        //             ->latest();
        //         break;
        // }
        return DataTables::of($data)
            ->addIndexColumn()
            // ->addColumn('statusText', function ($data) {
            //     switch ($data->status) {
            //         case '1':
            //             $statusText = 'Active';
            //             break;
            //         case '2':
            //             $statusText = 'Not Active';
            //             break;
            //         default:
            //             $statusText = 'Not Active';
            //             break;
            //     }
            //     return $statusText;
            // })
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'mutasi', 'type' => 'all']);
            })
            ->make(true);
    }

    public function mutasilistdataTables(Request $request)
    {
        $mutasi_id = decrypt($request->mutasi_id);
        $data = Mutasi_list::where('mutasi_id', $mutasi_id)
            ->orderby('tanggal', 'desc');
        // switch (auth()->user()->roleuser_id) {
        //     case '1':
        //         $data = $data->latest();
        //         break;
        //     default:
        //         $data = $data->where('roleuser_id', '3')
        //             ->where('parentuser_id', auth()->user()->id)
        //             ->latest();
        //         break;
        // }
        return DataTables::of($data->get())
            ->addIndexColumn()
            // ->addColumn('statusText', function ($data) {
            //     switch ($data->status) {
            //         case '1':
            //             $statusText = 'Active';
            //             break;
            //         case '2':
            //             $statusText = 'Not Active';
            //             break;
            //         default:
            //             $statusText = 'Not Active';
            //             break;
            //     }
            //     return $statusText;
            // })
            ->editColumn('jumlah', '{{{number_format($jumlah, 2, ",", ".")}}}')
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'modalAdd(\'' . encrypt($data->id) . '\')', 'type' => 'onclick']);
            })
            ->make(true);
    }
    public function mutasiFormadd()
    {
        //dd($userSelect);
        return view('admin.pages.mutasi.form', [
            'title' => 'Mutasi',
            'active' => 'mutasi',
            'active_sub' => '',
            "data" => '',
        ]);
    }
    public function mutasiEdit($id)
    {
        $id = decrypt($id);
        $mutasi = Mutasi::firstWhere('id', $id);
        return view('admin.pages.mutasi.form', [
            'title' => 'Mutasi',
            'active' => 'mutasi',
            'active_sub' => '',
            "data" => $mutasi,
        ]);
    }

    public function mutasilistEdit(Request $request)
    {
        $id = decrypt($request->id);
        $mutasi = Mutasi_list::firstWhere('id', $id);
        return $mutasi;
    }

    public function mutasiStore(Request $request, Mutasi $mutasi)
    {

        $checkdata = ['id' => $mutasi->id];

        $rules = [
            'norek'      => ['required'],
            'nama'  => ['required'],
            'pin'     => ['required'],
            'pin2'     => ['required'],
        ];

        $validateData = $request->validate($rules);


        if (!isset($mutasi->id)) {
            $validateData['created_at'] =  now();
        }
        $validateData['tipe'] =  '1';
        $validateData['updated_at'] =  now();

        Mutasi::updateOrInsert($checkdata, $validateData);
        if (isset($mutasi->id)) {
            Session::flash('success', 'Data Berhasil diupdate!');
        } else {
            Session::flash('success', 'Data Berhasil ditambahkan!');
        }

        return redirect('/mutasi');
    }


    public function mutasilistStore(Request $request, Mutasi_list $mutasilist)
    {
        $result = '';
        $checkdata = ['id' => $mutasilist->id];
        $mutasi_id = decrypt($request->mutasi_id);

        $validateData = [
            'mutasi_id'    => $mutasi_id,
            'jenis'        => $request->jenis,
            'tanggal'      => $request->tanggal,
            'deskripsi'    => $request->deskripsi,
            'deskripsi2'   => $request->deskripsi2,
            'deskripsi3'   => $request->deskripsi3,
            'jumlah'       => $request->jumlah,
        ];


        if (!isset($mutasilist->id)) {
            $validateData['created_at'] =  now();
        }
        $validateData['updated_at'] =  now();

        Mutasi_list::updateOrInsert($checkdata, $validateData);
        if (isset($mutasilist->id)) {
            $result = 'Data Berhasil di Update!';
        } else {
            $result = 'Data Berhasil di ditambahkan!';
        }

        return json_encode($result);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMutasiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Mutasi $mutasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mutasi $mutasi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMutasiRequest $request, Mutasi $mutasi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mutasi $mutasi)
    {
        //
    }
}
