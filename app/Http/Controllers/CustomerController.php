<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Fileexcel;
use App\Models\Distribusi;
use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Log_distribusi;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function customerFormimport()
    {
        return view('admin.pages.customer.form', [
            'title' => 'Customer',
            'active' => 'customer',
            'active_sub' => 'import',
            "data" => '',
            //"category" => User::all(),
        ]);
    }
    public function customerImport(Request $request)
    {
        //dd($request);
        $rules = [
            'kode'      => ['required', 'unique:fileexcels'],
            'nama_file'  => ['required', File::types(['xls', 'xlsx'])],
        ];
        $validateData = $request->validate($rules);
        $validateData['nama_file'] =  $request->file('nama_file')->getClientOriginalName();
        $validateData['user_id'] =  auth()->user()->id;
        $result = Fileexcel::create($validateData);
        if (isset($result->id)) {
            // Excel::import(new CustomerImport($result->id), $request->file('nama_file'));
            $import =  new CustomerImport($result->id);
            $import->import($request->file('nama_file'));

            // Menghitung data import & validasi
            $total_sukses = $import->getRowCount();
            $total_gagal = $import->failures()->count();
            $msg = '
                    Total data <span style="color:#00ff00;font-weight:600;">sukses import</span> = ' . $total_sukses . '<br>
                    Total data <span style="color:#f00;font-weight:600;">duplicate import</span> = ' . $total_gagal . '
                    ';

            // Menghapus jika tidak ada data yang terimport
            if ($total_sukses == '0') {
                Fileexcel::destroy($result->id);
            }

            return back()->withErrors(['msg' => $msg]);
        }
    }
    public function customerDistribusi()
    {
        $userSelect = User::where('status', '1');
        if (auth()->user()->roleuser_id == '2') {
            $userSelect = $userSelect->where('parentuser_id', auth()->user()->id)
                ->where('cabang_id', auth()->user()->cabang_id)
                ->where('roleuser_id', '3');
        } else if (auth()->user()->roleuser_id == '5') {
            $userSelect = $userSelect->where('cabang_id', auth()->user()->cabang_id)
                ->where(function ($query) {
                    $query->where('roleuser_id', '2')
                        ->orWhere('roleuser_id', '3');
                })
                ->orderby('roleuser_id', 'asc');
        } else {
            $userSelect = $userSelect->where(function ($query) {
                $query->where('roleuser_id', '2')
                    ->orWhere('roleuser_id', '3');
            })
                ->orderby('cabang_id', 'asc')
                ->orderby('roleuser_id', 'asc');
        }

        $produkSelect = Produk::where('status', '1')
            ->get();
        $fileExcel = Fileexcel::where('user_id', auth()->user()->id)
            ->without("Customer")
            ->get();
        return view('admin.pages.customer.distribusi', [
            'title' => 'Distribusi',
            'active' => 'distribusi',
            'active_sub' => 'import',
            "userData" => $userSelect->get(),
            "fileExceldata" => $fileExcel,
            "produkSelect" => $produkSelect,
            "data" => '',
            //"category" => User::all(),
        ]);
    }

    //**Tabel From pada page distribusi */
    public function customerDistribusifrom(Request $request)
    {

        $produk_id = auth()->user()->roleuser_id == '2' ? auth()->user()->produk_id : $request->produk_id;
        $tanggal = date('Y-m-d');
        // $tanggal = date('Y-m-d', strtotime('-5 days'));
        if ($request->fileexcel_id != 'today') {
            if ($request->tipe == 'RELOAD') {
                $lastDistribusi = DB::table('distribusis')
                    ->select('customer_id', DB::raw('MAX(id) as id'))
                    ->where('produk_id', $produk_id)
                    ->groupBy('customer_id');

                $data = DB::table($lastDistribusi, 'a')
                    ->select(
                        'customers.nama',
                        'customers.no_telp',
                        'customers.provider',
                    )
                    ->join('customers', 'customers.id', '=', 'a.customer_id')
                    ->leftjoin('distribusis as b', 'b.id', '=', 'a.id')
                    // jika local status nya 4 dan 5
                    // jika server status nya 12 dan 13
                    ->where(function ($query) {
                        $query->where('b.status', '12')
                            ->orWhere('b.status', '13');
                    })
                    ->whereDate('b.distribusi_at', '<>', $tanggal)
                    ->where('b.produk_id', $produk_id)
                    ->where('customers.fileexcel_id', $request->fileexcel_id)
                    ->where('customers.provider', $request->provider);

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('no_telp', '{{{substr($no_telp, 0, 6)}}}xxxx')
                    ->make(true);
            } else {
                $data = Customer::leftjoin('distribusis', function ($join) use ($produk_id, $tanggal) {
                    $join->on('customers.id', '=', 'distribusis.customer_id')
                        ->where(function ($query)  use ($produk_id, $tanggal) {
                            $query->where('distribusis.produk_id', '=', $produk_id)
                                ->orwhereDate('distribusis.distribusi_at', $tanggal);
                        });
                })
                    ->where('provider', $request->provider)
                    ->where('customers.fileexcel_id', $request->fileexcel_id)
                    ->where('distribusis.produk_id', null);

                return DataTables::of($data->get())
                    ->addIndexColumn()
                    ->editColumn('no_telp', '{{substr($no_telp, 0, 6)}}xxxx')
                    ->make(true);
            }
        } else {
            $data = Distribusi::join('customers', 'customers.id', '=', 'distribusis.customer_id')
                ->where('user_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where('distribusis.status', '0')
                        ->orWhere('distribusis.status', null);
                })
                ->where('distribusis.produk_id', $produk_id)
                ->where('customers.provider', $request->provider);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', '{{$customer[\'nama\']}}')
                ->addColumn('no_telp', '{{{substr($customer[\'no_telp\'], 0, 6)}}}xxxx')
                ->addColumn('perusahaan', '{{$customer[\'perusahaan\']}}')
                ->make(true);
        }
    }
    //**Tabel To pada page distribusi */
    public function customerDistribusito(Request $request)
    {
        $data = Distribusi::where('user_id', $request->user_id)
            ->where(function ($query) {
                $query->where('status', '0')
                    ->orWhere('status', null);
            });
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('customer.no_telp', '{{{substr($customer[\'no_telp\'], 0, 6)}}}xxxx')
            ->make(true);
    }
    //** Proses distribusi */
    public function customerDistribusiproses(Request $request)
    {
        $produk_id = auth()->user()->roleuser_id == '2' ? auth()->user()->produk_id : $request->produk_id;
        $tanggal = date('Y-m-d');
        $msglog = '';
        $msglog2 = '';
        $getUser = '';
        $msglogFileexcel = 'today';
        // $tanggal = date('Y-m-d', strtotime('-5 days'));
        if ($request->tipe == 'DISTRIBUSI') {
            if ($request->fileexcel_id == 'today') {
                $data = Distribusi::inRandomOrder()
                    ->select(
                        'distribusis.id as distribusi_id',
                        DB::raw('CONCAT("' . $request->user_id . '") as user_id'),
                        DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                        DB::raw('CONCAT("1") as bank_id'),
                        DB::raw('CONCAT("0") as status'),
                        DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                    )
                    ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
                    ->where('distribusis.user_id', auth()->user()->id)
                    ->where(function ($query) {
                        $query->where('distribusis.status', '0')
                            ->orWhere('distribusis.status', null);
                    })
                    ->where('customers.provider', $request->provider)
                    ->limit($request->total)
                    ->get();
                foreach ($data as $item) {
                    DB::table('distribusis')
                        ->where('id', $item->distribusi_id)
                        ->update(['user_id' => $request->user_id, 'updated_at' => now(), 'distribusi_at' => now()]);
                }
                $msglog2 = '';
            } else {
                $data = Customer::inRandomOrder()
                    ->select(
                        'customers.id as customer_id',
                        DB::raw('CONCAT("' . $request->user_id . '") as user_id'),
                        DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                        DB::raw('CONCAT("1") as bank_id'),
                        DB::raw('CONCAT("0") as status'),
                        DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                    )
                    ->leftjoin('distribusis', function ($join) use ($produk_id, $tanggal) {
                        $join->on('customers.id', '=', 'distribusis.customer_id')
                            ->where(function ($query)  use ($produk_id, $tanggal) {
                                $query->where('distribusis.produk_id', '=', $produk_id)
                                    ->orwhereDate('distribusis.distribusi_at', $tanggal);
                            });
                    })
                    ->where('customers.fileexcel_id', $request->fileexcel_id)
                    ->where('distribusis.produk_id', null)
                    ->where('customers.provider', $request->provider)
                    ->limit($request->total)
                    ->get();

                foreach ($data as $item) {
                    # code...
                    DB::table('customers')
                        ->where('id', $item->customer_id)
                        ->update(['status' => 1, 'updated_at' => now()]);
                }

                $distribusiInsert = $data->toArray();
                Distribusi::insert($distribusiInsert);

                $getfileexcel = Fileexcel::firstWhere('id', $request->fileexcel_id);
                $msglog2 = ' campaign ' . $getfileexcel->kode;
                $msglogFileexcel = $getfileexcel->kode;
            }
            // $data->update(['status' => 1]);
            $getUser = User::firstWhere('id', $request->user_id)->name;
            $msg = '
        Sukses mendistribusi data kepada <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span>
        ';

            $msglog = 'Sukses mendistribusi data' . $msglog2 . ' provider ' . $request->provider . ' kepada ' . $getUser . '';
        } else if ($request->tipe == 'TARIK DATA') {
            $data = Distribusi::inRandomOrder()
                ->select(
                    'distribusis.id as distribusi_id',
                    DB::raw('CONCAT("' . auth()->user()->id . '") as user_id'),
                    DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                    DB::raw('CONCAT("1") as bank_id'),
                    DB::raw('CONCAT("0") as status'),
                    DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                )
                ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
                ->where('distribusis.user_id', $request->user_id)
                ->where(function ($query) {
                    $query->where('distribusis.status', '0')
                        ->orWhere('distribusis.status', null);
                })
                ->where('customers.provider', $request->provider)
                ->limit($request->total)
                ->get();
            foreach ($data as $item) {
                # code...
                DB::table('distribusis')
                    ->where('id', $item->distribusi_id)
                    ->update(['user_id' => auth()->user()->id, 'updated_at' => now()]);
            }
            $getUser = User::firstWhere('id', $request->user_id)->name;
            $msg = '
        Sukses menarik data dari <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span>';
            $msglog = 'Sukses mendistribusi data' . $msglog2 . ' provider ' . $request->provider . ' kepada ' . $getUser . '';
        } else if ($request->tipe == 'RELOAD') {
            $lastDistribusi = DB::table('distribusis')
                ->select('customer_id', DB::raw('MAX(id) as id'))
                ->where('produk_id', $produk_id)
                ->groupBy('customer_id');

            $data = DB::table($lastDistribusi, 'a')
                ->select(
                    'b.customer_id',
                    DB::raw('CONCAT("' . $request->user_id . '") as user_id'),
                    DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                    DB::raw('CONCAT("1") as bank_id'),
                    DB::raw('CONCAT("0") as status'),
                    DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                )
                ->join('customers', 'customers.id', '=', 'a.customer_id')
                ->leftjoin('distribusis as b', 'b.id', '=', 'a.id')
                // jika local status nya 4 dan 5
                // jika server status nya 12 dan 13
                ->where(function ($query) {
                    $query->where('b.status', '12')
                        ->orWhere('b.status', '13');
                })
                ->whereDate('b.distribusi_at', '<>', $tanggal)
                ->where('b.produk_id', $produk_id)
                ->where('customers.fileexcel_id', $request->fileexcel_id)
                ->where('customers.provider', $request->provider)
                ->limit($request->total)
                ->get();
            $distribusiInsert = $data->toArray();
            $distribusiInsert = json_decode(json_encode($distribusiInsert), true);
            Distribusi::insert($distribusiInsert);

            $getfileexcel = Fileexcel::firstWhere('id', $request->fileexcel_id);
            $msglog2 = ' campaign ' . $getfileexcel->kode;
            $msglogFileexcel = $getfileexcel->kode;

            $getUser = User::firstWhere('id', $request->user_id)->name;
            $msg = '
        Sukses reload  data dari <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span>';
            $msglog = 'Sukses reload data' . $msglog2 . ' provider ' . $request->provider . ' kepada ' . $getUser . '';
        } else {
            $msg = 'Proses tidak ada';
            $msglog = 'Proses tidak ada';
        }
        $logInsert = [
            'tipe' => $request->tipe,
            'kode' => $msglogFileexcel,
            'provider' => $request->provider,
            'nama_sales' => $getUser,
            'deskripsi' => $msglog,
            'total' => $request->total,
            'user_id' => auth()->user()->id,
            'created_at' => now(),
        ];
        Log_distribusi::create($logInsert);
        $oldData = ['tipe' => $request->tipe, 'fileexcel_id' => $request->fileexcel_id, 'provider' => $request->provider];
        return back()->with(['msg' => $msg])->with(['oldData' => $oldData]);
    }
    //**Tabel Log Distribusi */
    public function logDistribusi(Request $request)
    {

        $data = Log_distribusi::select(
            'log_distribusis.*',
            'sales.name as salesnama',
        )
            ->join('users as sales', 'sales.id', '=', 'log_distribusis.user_id')
            ->orderby('created_at', 'desc');
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->where('user_id', auth()->user()->cabang_id);
        }
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', '{{{date("Y-m-d H:i:s",strtotime($created_at));}}}')
            ->make(true);
    }
    public function viewlogDistribusi()
    {
        return view('admin.pages.customer.logdistribusi', [
            'title' => 'History Distribusi',
            'active' => 'logdistribusi',
            'active_sub' => 'logdistribusi',
            "data" => '',
            //"category" => User::all(),
        ]);
    }
    public function viewCekdbr()
    {
        return view('admin.pages.customer.cekdbr', [
            'title' => 'Cek DBR',
            'active' => 'cekdbr',
            'active_sub' => 'cekdbr',
            "data" => '',
            //"category" => User::all(),
        ]);
    }
    public function cekDbr(Request $request)
    {
        $data = Distribusi::select(
            'distribusis.*',
            'parent.name as parentuser_nama'
        )->join('users as sales', 'sales.id', '=', 'distribusis.user_id')
            ->join('users as parent', 'parent.id', '=', 'sales.parentuser_id')
            ->where('distribusis.status', '1')
            ->whereDate('distribusis.updated_at', $request->tanggal)
            ->whereNotNull('distribusis.tipeproses');
        $data = $data->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'jmosip', 'type' => 'all']);
            })
            ->editColumn('jmoasli', '{{{number_format($jmoasli, 2, ",", ".")}}}')
            ->make(true);
    }
    public function viewCallhistory()
    {
        return view('admin.pages.customer.callhistory', [
            'title' => 'Call History',
            'active' => 'callhistory',
            'active_sub' => 'callhistory',
            "data" => '',
            //"category" => User::all(),
        ]);
    }
    public function callhistory(Request $request)
    {
        $data = Distribusi::select(
            'distribusis.*',
            'customers.nama as nama',
            'customers.no_telp as no_telp',
            'customers.provider as provider',
            'sales.name as salesnama',
            'statuscalls.nama as statustext',
            'fileexcels.kode as kode'
        )->join('users as sales', 'sales.id', '=', 'distribusis.user_id')
            ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
            ->where('distribusis.status', '<>', '0');
        if (auth()->user()->roleuser_id == '2') {
            $data = $data->where('sales.parentuser_id', auth()->user()->id);
        }
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->where('sales.cabang_id', auth()->user()->cabang_id);
        }
        $data = $data
            ->orderby('distribusis.updated_at', 'desc')
            ->without("Customer")
            ->without("User");
        //echo $data;
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('no_telp', '\'{{{substr($no_telp,-4)}}}')
            ->editColumn('updated_at', '{{{date("Y-m-d H:i:s",strtotime($updated_at));}}}')
            ->make(true);
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
    public function store(StoreCustomerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
