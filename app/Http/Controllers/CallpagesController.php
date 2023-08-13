<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Statuscall;
use App\Models\Subproduk;
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
            ->without("Customer")
            ->without("User")
            ->limit(1)
            ->get();

        $dataalltoday = Distribusi::where('user_id', auth()->user()->id)
            ->whereDate('distribusis.distribusi_at', $hariini)
            ->without("Customer")
            ->without("User")
            ->get();
        $dataall = Distribusi::where('user_id', auth()->user()->id)
            ->where('status', 0)
            ->without("Customer")
            ->without("User")
            ->get();
        // Get Data jenis 2
        $dataCall = Distribusi::where('user_id', auth()->user()->id)
            ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->where('distribusis.status', '<>', 0)
            ->where('statuscalls.jenis', '2')
            ->whereDate('distribusis.updated_at', $hariini)
            ->without("Customer")
            ->without("User")
            ->get();
        // Get Data jenis 1
        $dataCallout = Distribusi::where('user_id', auth()->user()->id)
            ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->where('distribusis.status', '<>', 0)
            ->where('statuscalls.jenis', '1')
            ->whereDate('distribusis.updated_at', $hariini)
            ->without("Customer")
            ->without("User")
            ->get();
        return view('sales.pages.call.index', [
            'title' => 'Call Page',
            'active' => 'call',
            'active_sub' => 'call',
            "data" => $data,
            "data_total" => $dataall->count(),
            "data_total_today" => $dataalltoday->count(),
            "dataCall" => $dataCall->count(),
            "dataCallout" => $dataCallout->count(),
            "dataCallout" => $dataCallout->count(),
            //"category" => User::all(),
        ]);
    }
    public function salesCallshow($id)
    {
        $upData = ['call_time' => now()];
        $id = decrypt($id);

        //dd($upData);

        $statusSelect = Statuscall::where('status', '1');
        if (auth()->user()->cabang_id == '4') {
            $statusSelect = $statusSelect->where('cabang_id', auth()->user()->cabang_id);
        } else {
            $statusSelect = $statusSelect->where('cabang_id', '0');
        }
        $statusSelect = $statusSelect->orderby('jenis', 'asc')
            ->orderby('id', 'asc')
            ->get();

        $subprodukSelect = Subproduk::where('status', '1')
            ->where('produk_id', auth()->user()->produk_id)
            ->get();
        $statusSelect1 = Statuscall::where('status', '1')
            ->where('jenis', '1')
            ->where('id', '<>', '15')
            ->where('id', '<>', '34')
            ->where('parentstatus_id', '0')
            ->where('cabang_id', auth()->user()->cabang_id)
            ->get();
        $statusSelect2 = Statuscall::where('status', '1')
            ->where('jenis', '2')
            ->where('cabang_id', auth()->user()->cabang_id)
            ->get();
        $statusSelect3 = Statuscall::where('status', '1')
            ->where('jenis', '1')
            ->where('parentstatus_id', '29')
            ->where('cabang_id', auth()->user()->cabang_id)
            ->get();

        $distribusi = Distribusi::select(
            'distribusis.*',
            'customers.nama',
            'customers.no_telp',
            'statuscalls.jenis as jenisstatus',
            'statuscalls.parentstatus_id',
        )
            ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->firstWhere('distribusis.id', $id);


        /** Update Call start time */
        if ($distribusi->status == '0') {
            Distribusi::where('id', $id)
                ->Update($upData);
        }
        return view('sales.pages.call.detail', [
            'title' => 'Call Page',
            'active' => 'call',
            'active_sub' => 'call',
            "data" => $distribusi,
            "subprodukSelect" => $subprodukSelect,
            "statusSelect" => $statusSelect,
            "statusSelect1" => $statusSelect1,
            "statusSelect2" => $statusSelect2,
            "statusSelect3" => $statusSelect3
        ]);
    }
    public function salesCallback(Request $request)
    {
        $data = Distribusi::select(
            'distribusis.*',
            'customers.nama',
            'customers.no_telp',
            'statuscalls.nama as statusText',
            'distribusis.deskripsi',
        )
            ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->where('distribusis.user_id', $request->user_id);
        if ($request->status == '2') {
            $data = $data->whereIn('distribusis.status', ['3', '14', '16']);
        } else if ($request->status == '1') {
            $data = $data->whereIn('distribusis.status', ['1', '15']);
        } else {
            $data = $data->whereIn('distribusis.status', ['2', '34']);
        }
        $data = $data->orderby('distribusis.updated_at', 'desc')
            ->limit(500);
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'call/detail', 'type' => 'sales']);
            })
            ->editColumn('customer.no_telp', '{{{substr($no_telp, 0, 6)}}}xxxx')
            ->make(true);
    }
    public function salescallStore(Request $request, $id)
    {
        $id = decrypt($id);
        $checkdata = ['id' => $id];

        // $rules = [
        //     'status'     => ['required'],
        //     'deskripsi'  => ['required'],
        // ];
        $custommessage = [];
        if (auth()->user()->cabang_id == '4') {
            $custommessage['subproduk_id.required'] = 'Mohon pilih produk';
            $custommessage['rbutton.required'] = 'Mohon pilih status call';
            $custommessage['status.required'] = 'Mohon pilih pengajuan';
            $custommessage['status1.required'] = 'Mohon pilih Alasan';
            $custommessage['status2.required'] = 'Mohon pilih Alasan';
            $custommessage['status3.required'] = 'Mohon pilih Detail Alasan';
            $custommessage['deskripsi.required'] = 'Mohon isikan remarks';
            if ($request->rbutton == '1') {
                if ($request->status == 'Ya') {
                    $rules = [
                        'rbutton'       => ['required'],
                        'status'        => ['required'],
                        'subproduk_id'     => ['required'],
                    ];
                } else if ($request->status == 'Pikir - Pikir') {
                    $rules = [
                        'rbutton'       => ['required'],
                        'status'        => ['required'],
                        'deskripsi'     => ['required'],
                    ];
                } else {
                    if ($request->status1 == '29') {
                        $rules = [
                            'rbutton'     => ['required'],
                            'status'        => ['required'],
                            'status3'     => ['required'],
                        ];
                    } else if ($request->status1 == '30') {
                        $rules = [
                            'rbutton'       => ['required'],
                            'status'        => ['required'],
                            'status1'       => ['required'],
                            'deskripsi'     => ['required'],
                        ];
                    } else {
                        $rules = [
                            'rbutton'       => ['required'],
                            'status'        => ['required'],
                            'status1'       => ['required'],
                        ];
                    }
                }
            } else {
                $rules = [
                    'rbutton'     => ['required'],
                    'status2'     => ['required'],
                ];
            }
        } else {
            $rules = [
                'status'     => ['required'],
            ];
        }
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

        $validateData = $request->validate($rules, $custommessage);
        if (auth()->user()->cabang_id == '4') {
            unset($validateData['rbutton']);
            if ($request->rbutton == '1') {
                if ($request->status == 'Ya') {
                    unset($validateData['status']);
                    $validateData['status'] = '15';
                } else if ($request->status == 'Pikir - Pikir') {
                    unset($validateData['status']);
                    $validateData['status'] = '34';
                } else {
                    if ($request->status1 == '29') {
                        unset($validateData['status3']);
                        $validateData['status'] = $request->status3;
                    } else {
                        unset($validateData['status1']);
                        $validateData['status'] = $request->status1;
                    }
                }
            } else {
                unset($validateData['status2']);
                $validateData['status'] = $request->status2;
            }
        }
        $validateData['deskripsi'] = $request->deskripsi;
        if (!isset($request->tipeproses)) {
            $validateData['end_call_time'] =  now();
            $validateData['updated_at'] =  now();
        }

        Distribusi::updateOrInsert($checkdata, $validateData);
        Session::flash('success', 'Data Berhasil diupdate!');
        return redirect('/call');
    }
}
