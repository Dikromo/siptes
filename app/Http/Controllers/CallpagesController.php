<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subproduk;
use App\Models\Distribusi;
use App\Models\Statuscall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CallpagesController extends Controller
{
    //
    private function checkDay($param, $param2)
    {
        $hasil = '';
        if (auth()->user()->cabang_id == '4') {
            if ($param2 == 'today') {
                switch (date('l', strtotime($param))) {
                    case 'Sunday':
                        $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        break;
                    default:
                        $hasil = $param;
                        break;
                }
            } else {
                switch (date('l', strtotime($param))) {
                    case 'Saturday':
                        $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        break;

                    case 'Sunday':
                        $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        break;
                        //     // Tanggalan merah
                        // case 'Thursday':
                        //     $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        //     break;
                        // case 'Wednesday':
                        //     $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        //     break;
                    default:
                        $hasil = $param;
                        break;
                }
            }
        } else {
            if ($param2 == 'today') {
                switch (date('l', strtotime($param))) {
                    case 'Sunday':
                        $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        break;
                    default:
                        $hasil = $param;
                        break;
                }
            } else {
                switch (date('l', strtotime($param))) {
                    case 'Saturday':
                        $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        break;

                    case 'Sunday':
                        $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        break;
                        //     // Tanggalan merah
                        // case 'Thursday':
                        //     $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        //     break;
                        // case 'Wednesday':
                        //     $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                        //     break;
                    default:
                        $hasil = $param;
                        break;
                }
            }
        }
        return $hasil;
    }
    public function salesCallpages()
    {
        //$cektoday = '2023-09-14';
        $cektoday = date('Y-m-d');
        $hariini = $this->checkDay(date('Y-m-d'), 'today');
        $h2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($hariini))), '');
        $h3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($hariini))), '');
        //$hariini = date('Y-m-d', strtotime('2023-07-21'));
        $data = Distribusi::select(
            'distribusis.id'
        )
            ->leftjoin('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->leftjoin('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
            ->where('distribusis.user_id', auth()->user()->id)
            ->where('distribusis.status', 0)
            ->orderByRaw(
                "distribusis.call_time desc,
                CASE date(fileexcels.prioritas_date)  
                    WHEN '" . $cektoday . "' THEN 1
                    ELSE 0
                END desc,CAST(fileexcels.prioritas AS UNSIGNED) ASC"
            )
            ->without("Customer")
            ->without("User")
            ->limit(1)
            ->get();

        // $dataalltoday = Distribusi::where('user_id', auth()->user()->id)
        //     ->whereDate('distribusis.distribusi_at', $hariini)
        //     ->without("Customer")
        //     ->without("User")
        //     ->get();
        // $dataall = Distribusi::where('user_id', auth()->user()->id)
        //     ->where('status', 0)
        //     ->without("Customer")
        //     ->without("User")
        //     ->get();
        // // Get Data jenis 2
        // $dataCall = Distribusi::where('user_id', auth()->user()->id)
        //     ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
        //     ->where('distribusis.status', '<>', 0)
        //     ->where('statuscalls.jenis', '2')
        //     ->whereDate('distribusis.updated_at', $hariini)
        //     ->without("Customer")
        //     ->without("User")
        //     ->get();
        // // Get Data jenis 1
        // $dataCallout = Distribusi::where('user_id', auth()->user()->id)
        //     ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
        //     ->where('distribusis.status', '<>', 0)
        //     ->where('statuscalls.jenis', '1')
        //     ->whereDate('distribusis.updated_at', $hariini)
        //     ->without("Customer")
        //     ->without("User")
        //     ->get();

        // $dataClosing = Distribusi::select(
        //     'distribusis.user_id',
        //     DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $hariini . '", 1, NULL)) AS closing1'),
        //     DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $h2 . '", 1, NULL)) AS closing2'),
        //     DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $h3 . '", 1, NULL)) AS closing3'),
        // )->where('user_id', auth()->user()->id)
        //     ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
        //     ->where('statuscalls.jenis', '1')
        //     ->where(function ($query)  use ($hariini, $h3) {
        //         $query->whereDate('distribusis.updated_at', '>=', $h3)
        //             ->whereDate('distribusis.updated_at', '<=', $hariini);
        //     })
        //     ->groupBy(DB::raw('1'))
        //     ->without("Customer")
        //     ->without("User")
        //     ->get();
        $datastatistik = User::select(
            'distribusis.user_id',
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $hariini . '", 1, NULL)) AS distribusi_today'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS nocall'),
            DB::raw('COUNT(IF((distribusis.status <> "0" AND statuscalls.jenis = "1") AND DATE(distribusis.updated_at) = "' . $hariini . '", 1, NULL)) AS callout'),
            DB::raw('COUNT(IF((distribusis.status <> "0" AND statuscalls.jenis = "2") AND DATE(distribusis.updated_at) = "' . $hariini . '", 1, NULL)) AS nocallout'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $hariini . '", 1, NULL)) AS closing1'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $h2 . '", 1, NULL)) AS closing2'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $h3 . '", 1, NULL)) AS closing3'),
        )
            ->leftjoin('distribusis', 'distribusis.user_id', '=', 'users.id')
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->where('users.id', auth()->user()->id)
            ->groupBy(DB::raw('1'))
            ->without("Customer")
            ->without("User")
            ->get();
        return view('sales.pages.call.index', [
            'title' => 'Call Page',
            'active' => 'call',
            'active_sub' => 'call',
            "data" => $data,
            // "data_total" => $dataall->count(),
            // "data_total_today" => $dataalltoday->count(),
            // "dataCall" => $dataCall->count(),
            // "dataCallout" => $dataCallout->count(),
            // "dataClosing" => $dataClosing,
            "datastatistik" => $datastatistik,
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
            DB::raw('IF(distribusis.namaktp is not null, distribusis.namaktp, customers.nama) as disnama'),
            DB::raw('IF(distribusis.perusahaan is not null, distribusis.perusahaan, customers.perusahaan) as disperusahaan'),
            'customers.nama',
            'customers.no_telp',
            'customers.perusahaan',
            'customers.kota',
            'statuscalls.jenis as jenisstatus',
            'statuscalls.parentstatus_id',
        )
            ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->firstWhere('distribusis.id', $id);


        /** Update Call start time */
        if ($distribusi->status == '0') {
            // Distribusi::where('id', $id)
            //     ->Update($upData);
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
    public function startCall(Request $request)
    {
        $upData = ['call_time' => now()];
        $distribusi = Distribusi::select(
            'distribusis.*',
            DB::raw('IF(distribusis.namaktp is not null, distribusis.namaktp, customers.nama) as disnama'),
            DB::raw('IF(distribusis.perusahaan is not null, distribusis.perusahaan, customers.perusahaan) as disperusahaan'),
            'customers.nama',
            'customers.no_telp',
            'customers.perusahaan',
            'customers.kota',
            'statuscalls.jenis as jenisstatus',
            'statuscalls.parentstatus_id',
        )
            ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->firstWhere('distribusis.id', $request->id);
        if ($distribusi->status == '0') {
            Distribusi::where('id', $request->id)
                ->Update($upData);
        }
        return json_encode('oke');
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
            ->leftjoin('users', 'users.id', '=', 'distribusis.user_id');
        if ($request->status == '4') {
            $data = $data->where('users.parentuser_id', $request->user_id);
        } else {
            $data = $data->where('distribusis.user_id', $request->user_id);
        }
        if ($request->status == '2') {
            $data = $data->whereIn('distribusis.status', ['3', '14', '16']);
        } else if ($request->status == '1') {
            $data = $data->whereIn('distribusis.status', ['1', '15']);
        } else if ($request->status == '4') {
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
            ->editColumn('updated_at', '{{{date("Y-m-d H:i:s",strtotime($updated_at));}}}')
            ->make(true);
    }
    public function salescallStore(Request $request, $id)
    {
        // dd($request->loan_apply);
        // exit;
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
                        'rbutton'           => ['required'],
                        'status'            => ['required'],
                        'subproduk_id'      => ['required'],
                        'loan_apply'        => ['required'],
                        'limit'             => ['required'],
                        'bank_penerbit'     => ['required'],
                        'mob'               => ['required'],
                    ];
                } else if ($request->status == 'Pikir - Pikir') {
                    $rules = [
                        'rbutton'           => ['required'],
                        'status'            => ['required'],
                        'limit'             => ['required'],
                        'bank_penerbit'     => ['required'],
                        'mob'               => ['required'],
                    ];
                } else {
                    if ($request->status1 == '29') {
                        if ($request->status3 == '41') {
                            $rules = [
                                'rbutton'       => ['required'],
                                'status'        => ['required'],
                                'status3'       => ['required'],
                                'deskripsi'     => ['required'],
                            ];
                        } else if ($request->status3 == '21' || $request->status3 == '22' || $request->status3 == '38') {
                            $rules = [
                                'rbutton'           => ['required'],
                                'status'            => ['required'],
                                'status3'           => ['required'],
                                'limit'             => ['required'],
                                'bank_penerbit'     => ['required'],
                                'mob'               => ['required'],
                            ];
                        } else {
                            $rules = [
                                'rbutton'       => ['required'],
                                'status'        => ['required'],
                                'status3'       => ['required'],
                            ];
                        }
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
            if (auth()->user()->produk_id == '2' || auth()->user()->produk_id == '4') {
                $rules['subproduk_id'] = ['required'];
            }
            if ($request->status == '1') {
                if (auth()->user()->produk_id == '1' &&  auth()->user()->roleuser_id == '2') {
                    $rules['tipeproses'] = 'required';
                    $rules['namaktp'] = 'required';
                    $rules['email'] = 'required';
                    $rules['nik'] = 'required';
                    $rules['dob'] = 'required';
                    $rules['perusahaan'] = 'required';
                    $rules['jabatan'] = 'required';
                    $rules['masakerja'] = 'required';
                    $rules['jmoasli'] = 'required';
                    $rules['loan_apply'] = 'required';
                    $rules['limit'] = 'required';
                    $rules['bank_penerbit'] = 'required';
                    $rules['mob'] = 'required';
                } else if (auth()->user()->produk_id == '1' &&  auth()->user()->roleuser_id != '2') {
                    $rules['namaktp'] = 'required';
                    $rules['email'] = 'required';
                    $rules['nik'] = 'required';
                    $rules['dob'] = 'required';
                    $rules['perusahaan'] = 'required';
                    $rules['jabatan'] = 'required';
                    $rules['masakerja'] = 'required';
                    $rules['jmoasli'] = 'required';
                    $rules['loan_apply'] = 'required';
                    $rules['limit'] = 'required';
                    $rules['bank_penerbit'] = 'required';
                    $rules['mob'] = 'required';
                }
            }
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
        $distribusi = Distribusi::select(
            'distribusis.call_time',
        )
            ->firstWhere('distribusis.id', $id);

        if ($distribusi->call_time == null) {
            $validateData['call_time'] =  now();
        }
        $validateData['d_parentuser_id'] = (auth()->user()->roleuser_id == '2') ? auth()->user()->id : auth()->user()->parentuser_id;
        $validateData['d_sm_id'] =  auth()->user()->sm_id;
        $validateData['d_um_id'] =  auth()->user()->um_id;
        $validateData['d_cabang_id'] =  auth()->user()->cabang_id;
        $validateData['deskripsi'] = $request->deskripsi;
        $validateData['nokantor'] =  $request->nokantor;
        if (!isset($request->tipeproses) && $request->tipe != 'apply') {
            $validateData['end_call_time'] =  now();
            $validateData['updated_at'] =  now();
        }

        Distribusi::updateOrInsert($checkdata, $validateData);
        Session::flash('success', 'Data Berhasil diupdate!');
        return redirect('/call');
    }
}
