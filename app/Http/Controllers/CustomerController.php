<?php

namespace App\Http\Controllers;

use App\Models\User;
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
                ->where('roleuser_id', '3');
        } else {
            $userSelect = $userSelect->where(function ($query) {
                $query->where('roleuser_id', '2')
                    ->orWhere('roleuser_id', '3');
            });
        }
        $fileExcel = Fileexcel::where('user_id', auth()->user()->id)
            ->get();
        return view('admin.pages.customer.distribusi', [
            'title' => 'Distribusi',
            'active' => 'distribusi',
            'active_sub' => 'import',
            "userData" => $userSelect->get(),
            "fileExceldata" => $fileExcel,
            "data" => '',
            //"category" => User::all(),
        ]);
    }

    //**Tabel From pada page distribusi */
    public function customerDistribusifrom(Request $request)
    {
        if ($request->fileexcel_id != 'today') {
            $data = Customer::where('fileexcel_id', $request->fileexcel_id)
                ->where(function ($query) {
                    $query->where('status', '0')
                        ->orWhere('status', null);
                })
                ->where('provider', $request->provider);

            return DataTables::of($data->get())
                ->addIndexColumn()
                ->editColumn('no_telp', '{{substr($no_telp, 0, 6)}}xxxx')
                ->make(true);
        } else {
            $data = Distribusi::join('customers', 'customers.id', '=', 'distribusis.customer_id')
                ->where('user_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where('distribusis.status', '0')
                        ->orWhere('distribusis.status', null);
                })
                ->where('customers.provider', $request->provider);
            return DataTables::of($data->get())
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
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->editColumn('customer.no_telp', '{{{substr($customer[\'no_telp\'], 0, 6)}}}xxxx')
            ->make(true);
    }
    //** Proses distribusi */
    public function customerDistribusiproses(Request $request)
    {
        if ($request->tipe == 'DISTRIBUSI') {
            if ($request->fileexcel_id == 'today') {
                $data = Distribusi::inRandomOrder()
                    ->select(
                        'distribusis.id as distribusi_id',
                        DB::raw('CONCAT("' . $request->user_id . '") as user_id'),
                        DB::raw('CONCAT("1") as product_id'),
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
            } else {
                $data = Customer::inRandomOrder()
                    ->select(
                        'id as customer_id',
                        DB::raw('CONCAT("' . $request->user_id . '") as user_id'),
                        DB::raw('CONCAT("1") as product_id'),
                        DB::raw('CONCAT("1") as bank_id'),
                        DB::raw('CONCAT("0") as status'),
                        DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                    )
                    ->where('fileexcel_id', $request->fileexcel_id)
                    ->where(function ($query) {
                        $query->where('status', '0')
                            ->orWhere('status', null);
                    })
                    ->where('provider', $request->provider)
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
            }
            // $data->update(['status' => 1]);
            $getUser = User::firstWhere('id', $request->user_id);
            $msg = '
        Sukses mendistribusi data kepada <span style="color:#00ff00;font-weight:600;">' . $getUser->name . '</span>
        ';
        } else if ($request->tipe == 'TARIK DATA') {
            $data = Distribusi::inRandomOrder()
                ->select(
                    'distribusis.id as distribusi_id',
                    DB::raw('CONCAT("' . auth()->user()->id . '") as user_id'),
                    DB::raw('CONCAT("1") as product_id'),
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
            $getUser = User::firstWhere('id', $request->user_id);
            $msg = '
        Sukses menarik data dari <span style="color:#00ff00;font-weight:600;">' . $getUser->name . '</span>';
        } else {
            $msg = 'Proses tidak ada';
        }
        return back()->withErrors(['msg' => $msg]);
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
