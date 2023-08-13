<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Statuscall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class StatuscallController extends Controller
{
    public function index()
    {
        return view(
            'admin.pages.config.statuscall.index',
            [
                'title' => 'Status Call',
                'active' => 'statuscall',
                'active_sub' => 'statuscall',
                'data' => Statuscall::latest()->paginate(10)->withQueryString()
            ]
        );
    }
    public function dataTables()
    {
        $data = Statuscall::select('statuscalls.*', 'cabangs.nama as nama_cabang')
            ->leftjoin('cabangs', 'cabangs.id', '=', 'statuscalls.cabang_id')
            ->latest();
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
                    ->with(['data' => $data, 'links' => 'statuscall', 'type' => 'all']);
            })
            ->make(true);
    }
    public function statuscallFormadd()
    {
        //dd($userSelect);
        $cabangSelect = Cabang::where('status', '1')
            ->get();
        return view('admin.pages.config.statuscall.form', [
            'title' => 'Status Call',
            'active' => 'statuscall',
            'active_sub' => '',
            "data" => '',
            "cabangSelect" => $cabangSelect,
        ]);
    }
    public function statuscallEdit($id)
    {
        $id = decrypt($id);
        $cabangSelect = Cabang::where('status', '1')
            ->get();
        $statuscall = Statuscall::firstWhere('id', $id);
        return view('admin.pages.config.statuscall.form', [
            'title' => 'Status Call',
            'active' => 'statuscall',
            'active_sub' => '',
            "data" => $statuscall,
            "cabangSelect" => $cabangSelect,
        ]);
    }


    public function statuscallStore(Request $request, Statuscall $statuscall)
    {

        $checkdata = ['id' => $statuscall->id];

        $rules = [
            'nama'           => ['required'],
            'cabang_id'      => ['required'],
        ];

        $validateData = $request->validate($rules);

        $validateData['status'] =  '1';
        $validateData['created_id'] =  '1';


        if (!isset($statuscall->id)) {
            $validateData['created_at'] =  now();
        }
        $validateData['updated_at'] =  now();

        Statuscall::updateOrInsert($checkdata, $validateData);
        if (isset($statuscall->id)) {
            Session::flash('success', 'Data Berhasil diupdate!');
        } else {
            Session::flash('success', 'Data Berhasil ditambahkan!');
        }

        return redirect('/statuscall');
    }
}
