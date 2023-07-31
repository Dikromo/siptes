<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Statuscall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CallpagesController extends Controller
{
    //
    public function salesCallpages()
    {
        $hariini = date('Y-m-d');
        //$hariini = date('Y-m-d', strtotime('2023-07-21'));
        $data = Distribusi::where('user_id', auth()->user()->id)
            ->where('status', 0)
            ->limit(1)
            ->get();
        $dataall = Distribusi::where('user_id', auth()->user()->id)
            ->where('status', 0)
            ->get();

        $dataCallout = Distribusi::where('user_id', auth()->user()->id)
            ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->where('distribusis.status', '<>', 0)
            ->where('statuscalls.jenis', '1')
            ->whereDate('distribusis.updated_at', $hariini)
            ->get();
        return view('sales.pages.call.index', [
            'title' => 'Call Page',
            'active' => 'call',
            'active_sub' => 'call',
            "data" => $data,
            "data_total" => $dataall,
            "dataCallout" => $dataCallout->count(),
            //"category" => User::all(),
        ]);
    }
    public function salesCallshow($id)
    {
        $upData = ['call_time' => now()];
        $id = decrypt($id);

        //dd($upData);
        /** Update Call start time */
        Distribusi::where('id', $id)
            ->Update($upData);
        $statusSelect = Statuscall::where('status', '1')
            ->orderby('jenis', 'asc')
            ->orderby('id', 'asc')
            ->get();

        $distribusi = Distribusi::firstWhere('id', $id);
        return view('sales.pages.call.detail', [
            'title' => 'Call Page',
            'active' => 'call',
            'active_sub' => 'call',
            "data" => $distribusi,
            "statusSelect" => $statusSelect
        ]);
    }
    public function salesCallback(Request $request)
    {
        $data = Distribusi::where('user_id', $request->user_id);
        if ($request->status != '1') {
            $data = $data->where(function ($query) {
                $query->where('status', '2')
                    ->orWhere('status', '3')
                    ->orWhere('status', '14');
            });
        } else {
            $data = $data->where('status', $request->status);
        }
        $data = $data->limit(1000);
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('statusText', function ($data) {
                switch ($data->status) {
                    case '1':
                        $statusText = 'Apply';
                        break;
                    case '2':
                        $statusText = 'Prospek';
                        break;
                    case '3':
                        $statusText = 'Call Back';
                        break;
                    case '14':
                        $statusText = 'Lanjut WA';
                        break;
                }
                return $statusText;
            })
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'call/detail', 'type' => 'sales']);
            })
            ->editColumn('customer.no_telp', '{{{substr($customer[\'no_telp\'], 0, 6)}}}xxxx')
            ->make(true);
    }
    public function salescallStore(Request $request, $id)
    {
        $id = decrypt($id);
        $checkdata = ['id' => $id];

        $rules = [
            'status'     => ['required'],
            'deskripsi'  => ['required'],
        ];
        if ($request->tipeproses == 'VIP') {
            $rules['tipeproses'] = 'required';
            $rules['nik'] = 'required';
            $rules['dob'] = 'required';
            $rules['perusahaan'] = 'required';
            $rules['jabatan'] = 'required';
            $rules['jmoasli'] = 'required';
        }

        if ($request->tipeproses == 'REGULER') {
            $rules['tipeproses'] = 'required';
            $rules['nik'] = 'required';
            $rules['dob'] = 'required';
        }

        $validateData = $request->validate($rules);

        if (!isset($request->tipeproses)) {
            $validateData['end_call_time'] =  now();
            $validateData['updated_at'] =  now();
        }

        Distribusi::updateOrInsert($checkdata, $validateData);
        Session::flash('success', 'Data Berhasil diupdate!');
        return redirect('/call');
    }
}
