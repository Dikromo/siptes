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
        $userSelect = User::where('status', '1')
            ->where('roleuser_id', '3')
            ->get();
        $fileExcel = Fileexcel::where('user_id', auth()->user()->id)
            ->get();
        return view('admin.pages.customer.distribusi', [
            'title' => 'Distribusi',
            'active' => 'distribusi',
            'active_sub' => 'import',
            "userData" => $userSelect,
            "fileExceldata" => $fileExcel,
            "data" => '',
            //"category" => User::all(),
        ]);
    }

    //**Tabel From pada page distribusi */
    public function customerDistribusifrom(Request $request)
    {
        $data = Customer::where('fileexcel_id', $request->fileexcel_id)
            ->where(function ($query) {
                $query->where('status', '0')
                    ->orWhere('status', null);
            });
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('no_telp', '{{substr($no_telp, 0, 6)}}xxxx')
            ->make(true);
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
            ->limit($request->total)
            ->get();

        foreach ($data as $item) {
            # code...
            DB::table('customers')
                ->where('id', $item->customer_id)
                ->update(['status' => 1, 'updated_at' => now()]);
        }
        // $data->update(['status' => 1]);
        $distribusiInsert = $data->toArray();
        Distribusi::insert($distribusiInsert);
        $getUser = User::firstWhere('id', $request->user_id);
        $msg = '
        Sukses mendistribusi data kepada <span style="color:#00ff00;font-weight:600;">' . $getUser->name . '</span>
        ';
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
