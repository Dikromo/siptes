<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Fileexcel;
use App\Models\Distribusi;
use App\Models\Statuscall;
use App\Models\Setupreload;
use Illuminate\Http\Request;
use App\Models\Log_distribusi;
use App\Imports\CustomerImport;
use App\Models\group_fileexcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
    public function customerDistribusi(Request $request)
    {
        $hariini = date('Y-m-d');
        // dd($hariini);
        $userSelect = User::select(
            'users.*',
            'spv.nickname as spvnickname'
        )
            ->leftjoin('users as spv', 'spv.id', '=', 'users.parentuser_id')
            ->where('users.status', '1')
            ->where('users.flagdistri', '1')
            ->where(function ($query) use ($hariini) {
                $query->whereNull('users.flag_hadir')
                    ->orWhereRaw('date(users.flag_hadir) <> "' . $hariini . '"');
            });
        if (auth()->user()->roleuser_id == '2') {
            $userSelect = $userSelect->where('users.parentuser_id', auth()->user()->id)
                ->where('users.cabang_id', auth()->user()->cabang_id)
                ->where('users.roleuser_id', '3');
        } else if (auth()->user()->roleuser_id == '5') {
            $userSelect = $userSelect->where('users.sm_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where('users.roleuser_id', '2')
                        ->orWhere('users.roleuser_id', '3');
                })
                ->orderby('users.roleuser_id', 'asc');
        } else if (auth()->user()->roleuser_id == '6') {
            if (auth()->user()->cabang_id == 4) {
                $userSelect = $userSelect->where('users.cabang_id', auth()->user()->cabang_id)
                    ->where(function ($query) {
                        $query->where('users.roleuser_id', '2')
                            ->orWhere('users.roleuser_id', '3');
                    });
            } else {
                $userSelect = $userSelect->where('users.um_id', auth()->user()->id)
                    ->where(function ($query) {
                        $query->where('users.roleuser_id', '2')
                            ->orWhere('users.roleuser_id', '3');
                    })
                    ->orderby('users.roleuser_id', 'asc');
            }
        } else {
            $userSelect = $userSelect->orderby('users.cabang_id', 'asc')
                ->orderby('users.roleuser_id', 'asc');
        }

        $produkSelect = Produk::where('status', '1')
            ->get();

        $lastDistribusi = DB::table('distribusis')
            ->select('customer_id', DB::raw('MAX(id) as id'))
            ->groupBy('customer_id');

        $fileExcel = DB::table('fileexcels')
            ->select(
                'fileexcels.id',
                'fileexcels.kode',
                DB::raw('COUNT(IF(customers.status = "0", 1, NULL)) AS total_data'),
            )
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                $join->on('customers.id', '=', 'a.customer_id');
            });
        $fileExcel =    $fileExcel->where('fileexcels.user_id', auth()->user()->id)
            ->where('customers.provider', '<>', 'Tidak Ditemukan')
            ->orderby('fileexcels.id', 'desc')
            ->groupBy(DB::raw('1,2'))
            ->get();

        $group_fileexcels = DB::table('group_fileexcels')
            ->select(
                'group_fileexcels.id',
                'group_fileexcels.nama',
                DB::raw('COUNT(IF(customers.status = "0", 1, NULL)) AS total_data'),
            )
            ->join('fileexcels', 'group_fileexcels.id', '=', 'fileexcels.group_id')
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                $join->on('customers.id', '=', 'a.customer_id');
            });
        $group_fileexcels = $group_fileexcels->where('group_fileexcels.created_id', auth()->user()->id)
            ->where('customers.provider', '<>', 'Tidak Ditemukan')
            ->orderby('fileexcels.id', 'desc')
            ->groupBy(DB::raw('1,2'))
            ->get();
        return view('admin.pages.customer.distribusi', [
            'title' => 'Distribusi',
            'active' => 'distribusi',
            'active_sub' => 'import',
            "userData" => $userSelect->get(),
            "fileExceldata" => $fileExcel,
            "groupfileexcelsdata" => $group_fileexcels,
            "produkSelect" => $produkSelect,
            "data" => '',
            "get" => isset($request) ? $request : '',
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
                $defaultreload = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                // $defaultreload = ['3', '11', '12', '13', '14', '16', '17', '18', '19', '25', '26', '27', '28', '35', '37'];
                if ($request->group_fileexcels_id != '') {
                    $setupReloaddata = Setupreload::where('group_id', $request->group_fileexcels_id)->where('status', '1');
                } else {
                    $setupReloaddata = Setupreload::where('fileexcel_id', $request->fileexcel_id)->where('status', '1');
                }
                if ($setupReloaddata->count() > 0) {
                    $defaultreload = [];
                    foreach ($setupReloaddata->get() as $item) {
                        # code...
                        $defaultreload[] = $item->statuscall_id;
                    }
                }


                $lastDistribusi = DB::table('distribusis')
                    ->select('customer_id', DB::raw('MAX(id) as id'), DB::raw('COUNT(id) AS tot'))
                    ->where('produk_id', $produk_id)
                    ->groupBy('customer_id')
                    ->orderBy('tot', 'asc');

                $data = DB::table($lastDistribusi, 'a')
                    ->select(
                        'customers.nama',
                        'customers.no_telp',
                        'customers.provider',
                        'fileexcels.kode',
                    )
                    ->join('customers', 'customers.id', '=', 'a.customer_id')
                    ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
                    ->leftjoin('distribusis as b', 'b.id', '=', 'a.id')
                    // jika local status nya 4 dan 5
                    // jika server status nya 12 dan 13
                    ->whereIn('b.status', $defaultreload)
                    ->whereDate('b.distribusi_at', '<>', $tanggal)
                    ->where('b.produk_id', $produk_id);
                if ($request->group_fileexcels_id != '') {
                    $data = $data->where('fileexcels.group_id', $request->group_fileexcels_id);
                } else {
                    $data = $data->where('customers.fileexcel_id', $request->fileexcel_id);
                }
                if (isset($request->user_id)) {
                    $data =   $data->whereNotIn('b.user_id', $request->user_id);
                }
                if ($request->provider == 'NON-SIMPATI') {
                    $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                        ->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else if ($request->provider == 'ALL-PROVIDER') {
                    $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else {
                    $data =   $data->where('customers.provider', $request->provider);
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('no_telp', '{{{substr($no_telp, 0, 6)}}}xxxx')
                    ->make(true);
            } else {
                $data = Customer::select(
                    'customers.nama',
                    'customers.no_telp',
                    'customers.provider',
                    'fileexcels.kode',
                )->leftjoin('distribusis', function ($join) use ($produk_id, $tanggal) {
                    $join->on('customers.id', '=', 'distribusis.customer_id')
                        ->where(function ($query)  use ($produk_id, $tanggal) {
                            $query->where('distribusis.produk_id', '=', $produk_id)
                                ->orwhereDate('distribusis.distribusi_at', $tanggal);
                        });
                })
                    ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id');

                if ($request->provider == 'NON-SIMPATI') {
                    $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                        ->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else if ($request->provider == 'ALL-PROVIDER') {
                    $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else {
                    $data =   $data->where('provider', $request->provider);
                }

                if ($request->group_fileexcels_id != '') {
                    $data = $data->where('fileexcels.group_id', $request->group_fileexcels_id);
                } else {
                    $data = $data->where('customers.fileexcel_id', $request->fileexcel_id);
                }
                $data = $data->where('distribusis.produk_id', null);

                return DataTables::of($data->get())
                    ->addIndexColumn()
                    ->editColumn('no_telp', '{{substr($no_telp, 0, 6)}}xxxx')
                    ->make(true);
            }
        } else {
            $data = Distribusi::select(
                'customers.nama',
                'customers.no_telp',
                'customers.provider',
                'fileexcels.kode',
            )
                ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
                ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
                ->where('distribusis.user_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where('distribusis.status', '0')
                        ->orWhere('distribusis.status', null);
                })
                ->where('distribusis.produk_id', $produk_id);
            if ($request->provider == 'NON-SIMPATI') {
                $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                    ->where('customers.provider', '<>', 'Tidak Ditemukan');
            } else if ($request->provider == 'ALL-PROVIDER') {
                $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
            } else {
                $data =   $data->where('customers.provider', $request->provider);
            }

            $data = $data->without("Customer")
                ->without("User");
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('no_telp', '{{substr($no_telp, 0, 6)}}xxxx')
                ->make(true);
        }
    }
    //**Tabel To pada page distribusi */
    public function customerDistribusito(Request $request)
    {
        $data = Distribusi::select(
            'customers.nama',
            'customers.no_telp',
            'customers.provider',
            'fileexcels.kode',
        )
            ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
            ->whereIn('distribusis.user_id',  $request->user_id)
            //->where('distribusis.user_id', $request->user_id)
            ->where(function ($query) {
                $query->where('distribusis.status', '0')
                    ->orWhere('distribusis.status', null);
            })
            ->without("Customer")
            ->without("User");
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('no_telp', '{{{substr($no_telp, 0, 6)}}}xxxx')
            ->make(true);
    }
    //** Proses distribusi */
    public function customerDistribusiproses(Request $request)
    {
        $produk_id = auth()->user()->roleuser_id == '2' ? auth()->user()->produk_id : $request->produk_id;
        $tanggal = date('Y-m-d');
        $msg = '';
        $msglog = '';
        $msglog2 = '';
        $getUser = '';
        $msglogFileexcel = 'today';
        // $tanggal = date('Y-m-d', strtotime('-5 days'));
        foreach ($request->user_id as $user_id) {

            if ($request->tipe == 'DISTRIBUSI') {
                if ($request->fileexcel_id == 'today') {
                    $data = Distribusi::inRandomOrder()
                        ->select(
                            'distribusis.id as distribusi_id',
                            DB::raw('CONCAT("' . $user_id . '") as user_id'),
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
                        });
                    if ($request->provider == 'NON-SIMPATI') {
                        $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                            ->where('customers.provider', '<>', 'Tidak Ditemukan');
                    } else if ($request->provider == 'ALL-PROVIDER') {
                        $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                    } else {
                        $data =   $data->where('customers.provider', $request->provider);
                    }
                    $data = $data->limit($request->total)
                        ->get();
                    foreach ($data as $item) {
                        DB::table('distribusis')
                            ->where('id', $item->distribusi_id)
                            ->update(['user_id' => $user_id, 'updated_at' => now(), 'distribusi_at' => now()]);
                    }
                    $msglog2 = '';
                } else {
                    $data = Customer::inRandomOrder()
                        ->select(
                            'customers.id as customer_id',
                            DB::raw('CONCAT("' . $user_id . '") as user_id'),
                            DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                            DB::raw('CONCAT("1") as bank_id'),
                            DB::raw('CONCAT("0") as status'),
                            DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                        )
                        ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
                        ->leftjoin('distribusis', function ($join) use ($produk_id, $tanggal) {
                            $join->on('customers.id', '=', 'distribusis.customer_id')
                                ->where(function ($query)  use ($produk_id, $tanggal) {
                                    $query->where('distribusis.produk_id', '=', $produk_id)
                                        ->orwhereDate('distribusis.distribusi_at', $tanggal);
                                });
                        });
                    if ($request->group_fileexcels_id != '') {
                        $data = $data->where('fileexcels.group_id', $request->group_fileexcels_id);
                    } else {
                        $data = $data->where('customers.fileexcel_id', $request->fileexcel_id);
                    }
                    $data = $data->where('distribusis.produk_id', null);
                    if ($request->provider == 'NON-SIMPATI') {
                        $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                            ->where('customers.provider', '<>', 'Tidak Ditemukan');
                    } else if ($request->provider == 'ALL-PROVIDER') {
                        $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                    } else {
                        $data =   $data->where('customers.provider', $request->provider);
                    }
                    $data = $data->limit($request->total)
                        ->get();

                    foreach ($data as $item) {
                        # code...
                        DB::table('customers')
                            ->where('id', $item->customer_id)
                            ->update(['status' => 1, 'updated_at' => now()]);
                    }

                    $distribusiInsert = $data->toArray();
                    Distribusi::insert($distribusiInsert);

                    if ($request->group_fileexcels_id != '') {
                        $getfileexcel = group_fileexcel::firstWhere('id', $request->group_fileexcels_id);
                        $msglog2 = ' Group campaign ' . $getfileexcel->nama;
                        $msglogFileexcel = $getfileexcel->nama;
                    } else {
                        $getfileexcel = Fileexcel::firstWhere('id', $request->fileexcel_id);
                        $msglog2 = ' campaign ' . $getfileexcel->kode;
                        $msglogFileexcel = $getfileexcel->kode;
                    }
                }
                // $data->update(['status' => 1]);
                $getUser = User::firstWhere('id', $user_id)->name;
                $msg .= '
        Sukses mendistribusi data kepada <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span><br>
        ';

                $msglog = 'Sukses mendistribusi data' . $msglog2 . ' provider ' . $request->provider . ' kepada ' . $getUser . '';
            } else if ($request->tipe == 'TARIK DATA' || $request->tipe == 'TARIK DATA BY ADMIN' || $request->tipe == 'TARIK DATA ALL CAMPAIGN') {
                $data = Distribusi::inRandomOrder();
                if ($request->tipe == 'TARIK DATA ALL CAMPAIGN') {
                    $data->select(
                        'distribusis.user_id as user_id',
                        DB::raw('GROUP_CONCAT(distribusis.id) distribusi_id')

                    );
                } else {
                    $data = $data->select(
                        'distribusis.id as distribusi_id',
                        DB::raw('CONCAT("' . auth()->user()->id . '") as user_id'),
                        DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                        DB::raw('CONCAT("1") as bank_id'),
                        DB::raw('CONCAT("0") as status'),
                        DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                    );
                }
                $data = $data->join('customers', 'customers.id', '=', 'distribusis.customer_id')
                    ->join('fileexcels', 'customers.fileexcel_id', '=', 'fileexcels.id')
                    ->where('distribusis.user_id', $user_id)
                    ->whereNull('distribusis.call_time')
                    ->where(function ($query) {
                        $query->where('distribusis.status', '0')
                            ->orWhere('distribusis.status', null);
                    });
                if ($request->provider == 'NON-SIMPATI') {
                    $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                        ->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else if ($request->provider == 'ALL-PROVIDER') {
                    $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else {
                    $data =   $data->where('customers.provider', $request->provider);
                }
                if ($request->fileexcel_id != 'today') {
                    if ($request->group_fileexcels_id != '') {
                        $data = $data->where('fileexcels.group_id', $request->group_fileexcels_id);
                    } else {
                        $data = $data->where('customers.fileexcel_id', $request->fileexcel_id);
                    }
                }
                if ($request->fileexcel_id == 'today') {
                    if ($request->tipe == 'TARIK DATA BY ADMIN') {
                        $data = $data->where('fileexcels.user_id', '31');
                    }
                }
                $data = $data->limit($request->total);
                if ($request->tipe == 'TARIK DATA ALL CAMPAIGN') {
                    $data = $data->groupBy(DB::raw('1'));
                }
                $data = $data->get();
                if ($request->fileexcel_id == 'today') {
                    foreach ($data as $item) {
                        # code...
                        if ($request->tipe == 'TARIK DATA ALL CAMPAIGN') {
                            foreach ($data as $item) {
                                $myArray = explode(',', $item->distribusi_id);
                            }
                            if (count($myArray) > 0) {
                                DB::table('distribusis')->whereIn('id', $myArray)->delete();
                            }
                        } else {
                            DB::table('distribusis')
                                ->where('id', $item->distribusi_id)
                                ->update(['user_id' => auth()->user()->id, 'updated_at' => now()]);
                        }
                    }
                } else {
                    foreach ($data as $item) {
                        DB::table('distribusis')->where('id', $item->distribusi_id)->delete();
                    }
                }

                $getUser = User::firstWhere('id', $user_id)->name;
                $msg .= '
        Sukses menarik data dari <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span><br>';
                $msglog = 'Sukses menarik data' . $msglog2 . ' provider ' . $request->provider . ' kepada ' . $getUser . '';
            } else if ($request->tipe == 'RELOAD') {
                $defaultreload = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                // $defaultreload = ['3', '11', '12', '13', '14', '16', '17', '18', '19', '25', '26', '27', '28', '35', '37'];
                if ($request->group_fileexcels_id != '') {
                    $setupReloaddata = Setupreload::where('group_id', $request->group_fileexcels_id)->where('status', '1');
                } else {
                    $setupReloaddata = Setupreload::where('fileexcel_id', $request->fileexcel_id)->where('status', '1');
                }
                if ($setupReloaddata->count() > 0) {
                    $defaultreload = [];
                    foreach ($setupReloaddata->get() as $item) {
                        # code...
                        $defaultreload[] = $item->statuscall_id;
                    }
                }

                $lastDistribusi = DB::table('distribusis')
                    ->select('customer_id', DB::raw('MAX(id) as id'), DB::raw('COUNT(id) AS tot'))
                    ->where('produk_id', $produk_id)
                    ->groupBy('customer_id')
                    ->orderBy('tot', 'asc');

                $data = DB::table($lastDistribusi, 'a')
                    ->select(
                        'b.customer_id',
                        DB::raw('CONCAT("' . $user_id . '") as user_id'),
                        DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                        DB::raw('CONCAT("1") as bank_id'),
                        DB::raw('CONCAT("0") as status'),
                        DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                    )
                    ->join('customers', 'customers.id', '=', 'a.customer_id')
                    ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
                    ->leftjoin('distribusis as b', 'b.id', '=', 'a.id')
                    // jika local status nya 4 dan 5
                    // jika server status nya 12 dan 13
                    // ->where(function ($query) {
                    //     $query->where('b.status', '12')
                    //         ->orWhere('b.status', '13');
                    // })
                    //->whereIn('b.status', ['12', '13', '18', '26'])
                    ->whereIn('b.status', $defaultreload)
                    ->whereDate('b.distribusi_at', '<>', $tanggal)
                    ->where('b.produk_id', $produk_id);
                if ($request->group_fileexcels_id != '') {
                    $data = $data->where('fileexcels.group_id', $request->group_fileexcels_id);
                } else {
                    $data = $data->where('customers.fileexcel_id', $request->fileexcel_id);
                }
                if (isset($request->user_id)) {
                    $data =   $data->whereNotIn('b.user_id', $request->user_id);
                }
                if ($request->provider == 'NON-SIMPATI') {
                    $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                        ->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else if ($request->provider == 'ALL-PROVIDER') {
                    $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else {
                    $data =   $data->where('customers.provider', $request->provider);
                }
                $data = $data->limit($request->total)
                    ->get();
                $distribusiInsert = $data->toArray();
                $distribusiInsert = json_decode(json_encode($distribusiInsert), true);
                Distribusi::insert($distribusiInsert);

                if ($request->group_fileexcels_id != '') {
                    $getfileexcel = group_fileexcel::firstWhere('id', $request->group_fileexcels_id);
                    $msglog2 = ' Group campaign ' . $getfileexcel->nama;
                    $msglogFileexcel = $getfileexcel->nama;
                } else {
                    $getfileexcel = Fileexcel::firstWhere('id', $request->fileexcel_id);
                    $msglog2 = ' campaign ' . $getfileexcel->kode;
                    $msglogFileexcel = $getfileexcel->kode;
                }

                $getUser = User::firstWhere('id', $user_id)->name;
                $msg .= '
        Sukses reload  data dari <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span><br>';
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
        }
        $oldData = ['tipe' => $request->tipe, 'group_fileexcels_id' => $request->group_fileexcels_id, 'fileexcel_id' => $request->fileexcel_id, 'provider' => $request->provider];
        return back()->with(['msg' => $msg])->with(['oldData' => $oldData]);
    }
    //**Tabel Log Distribusi */
    public function logDistribusi(Request $request)
    {

        $data = Log_distribusi::select(
            'log_distribusis.*',
            'sales.name as salesnama',
        )
            ->join('users as sales', 'sales.id', '=', 'log_distribusis.user_id');
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->where('user_id', auth()->user()->id);
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
            DB::raw('IF(distribusis.namaktp is not null, distribusis.namaktp, customers.nama) as namacust'),
            DB::raw('IF(parent.name is not null, parent.name, sales.name) as parentuser_nama'),
        )
            ->join('users as sales', 'sales.id', '=', 'distribusis.user_id')
            ->join('customers', 'distribusis.customer_id', '=', 'customers.id')
            ->leftjoin('users as parent', 'parent.id', '=', 'sales.parentuser_id')
            ->where('distribusis.status', '1')
            ->where('distribusis.produk_id', '1')
            ->whereDate('distribusis.updated_at', $request->tanggal)
            ->orderBy('updated_at', 'desc');
        $data = $data->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'jmosip', 'type' => 'all']);
            })
            ->editColumn('updated_at', '{{{date("Y-m-d",strtotime($updated_at));}}}')
            // ->editColumn('jmoasli', '{{{number_format($jmoasli, 2, ",", ".")}}}')
            ->make(true);
    }
    public function viewCallhistory(Request $request)
    {
        $userSelect = User::where('status', '1');
        if (auth()->user()->roleuser_id == '2') {
            $userSelect = $userSelect->where('parentuser_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where('roleuser_id', '2')
                        ->orWhere('roleuser_id', '3');
                });
        } else if (auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '6') {
            $userSelect = $userSelect->where('cabang_id', auth()->user()->cabang_id)
                ->where(function ($query) {
                    $query->where('roleuser_id', '2')
                        ->orWhere('roleuser_id', '3');
                });
        } else if (auth()->user()->roleuser_id == '5') {
            $userSelect = $userSelect->where('sm_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where('roleuser_id', '2')
                        ->orWhere('roleuser_id', '3');
                });
        } else {
            $userSelect = $userSelect
                ->where(function ($query) {
                    $query->where('roleuser_id', '2')
                        ->orWhere('roleuser_id', '3');
                });
        }
        $statusSelect = Statuscall::where('status', '1')
            ->where('jenis', '1')
            ->whereIn('id', ['2', '3']);
        return view('admin.pages.customer.callhistory', [
            'title' => 'Call History',
            'active' => 'callhistory',
            'active_sub' => 'callhistory',
            "data" => '',
            "get" => isset($request) ? $request : '',
            "userSelect" => $userSelect->get(),
            "statusSelect" => $statusSelect->get(),
            //"category" => User::all(),
        ]);
    }
    public function callhistory(Request $request)
    {
        $paramStatus = $request->status != '' ? (string)decrypt($request->status) : '';
        $idcampaign = $request->idcampaign != '' ? (string)decrypt($request->idcampaign) : '';
        if ($request->pageon == '' || $request->pageon == 'today' || $request->pageon == 'reload') {
            if ($request->pageon == '') {
                $pageon = '';
            } else if ($request->pageon == 'reload') {
                $pageon = 'reload';
            } else {
                $pageon = 'today';
            }
        } else {
            $pageon = (string)decrypt($request->pageon);
        }
        //$pageon = $request->pageon == '' || $request->pageon == 'today'  ? '' : (string)decrypt($request->pageon);

        if ($idcampaign != '') {
            $lastDistribusi = DB::table('distribusis')
                ->select(
                    'customer_id',
                    DB::raw(
                        "COALESCE(
                            MAX(
                                CASE
                                WHEN distribusis.status = '1' OR distribusis.status = '15' 
                                THEN distribusis.id
                            END), MAX(distribusis.id)) AS id"
                    )
                )
                ->join('customers', 'customers.id', '=', 'distribusis.customer_id')
                ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
                ->join('users', 'users.id', '=', 'distribusis.user_id');
            if (auth()->user()->roleuser_id != '1') {
                $lastDistribusi = $lastDistribusi->whereRaw('users.cabang_id = "' . auth()->user()->cabang_id . '"')
                    ->whereRaw('fileexcels.id= "' . $idcampaign . '"');
                // if ($pageon == 'today') {
                //     $lastDistribusi = $lastDistribusi->whereRaw('DATE(distribusis.updated_at) >= "' . $request->fromtanggal . '" ')
                //         ->whereRaw('DATE(distribusis.updated_at) <= "' . $request->totanggal . '" ');
                // }
            }
            $lastDistribusi = $lastDistribusi->groupBy('customer_id');
        }

        $data = Distribusi::select(
            'distribusis.*',
            'customers.nama as nama',
            'customers.no_telp as no_telp',
            'customers.provider as provider',
            DB::raw('UPPER(cabangs.nama) as cabangnama'),
            DB::raw('UPPER(sales.name) as salesnama'),
            DB::raw('UPPER(IF(parentuser.name is not null, parentuser.name, sales.name)) as spvnama'),
            DB::raw('UPPER(IF(parentuser.nickname is not null, parentuser.nickname, sales.nickname)) as spvnickname'),
            DB::raw('UPPER(IF(statuscalls.jenis = "1", "Terhubung", "Tidak Terhubung")) as statuscall_jenis'),
            DB::raw('UPPER(sm.name) as smname'),
            DB::raw('UPPER(sm.nickname) as smnickname'),
            DB::raw('CONCAT(sales.name," (",parentuser.name,") ") AS csalesnama'),
            'statuscalls.nama as statustext',
            'subproduks.nama as subproduktext',
            'fileexcels.kode as kode',
            DB::raw('timediff(distribusis.updated_at,distribusis.call_time) as selisih')
        )
            ->join('users as sales', 'sales.id', '=', 'distribusis.user_id')
            ->leftjoin('users as parentuser', 'parentuser.id', '=', 'sales.parentuser_id')
            ->leftjoin('users as sm', 'sm.id', '=', 'sales.sm_id')
            ->join('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->leftjoin('cabangs', 'cabangs.id', '=', 'sales.cabang_id')
            ->leftjoin('produks', 'produks.id', '=', 'distribusis.produk_id')
            ->leftjoin('subproduks', 'subproduks.id', '=', 'distribusis.subproduk_id')
            ->leftjoin('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->leftjoin('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
            ->where('distribusis.status', '<>', '0');
        if ($request->user_id != '' || ($request->user_id == '' && $paramStatus != '')) {
            if ($request->user_id != '') {
                $data = $data->where('sales.id', $request->user_id);
            }
            if ($paramStatus == '1') {
                $data = $data->where('statuscalls.jenis', $paramStatus);
            }
            if ($paramStatus == '4') {
                $data = $data->where('statuscalls.jenis', '2');
            }
            if ($paramStatus == '2') {
                $data = $data->whereIn('distribusis.status', ['1', '15']);
            }
            if ($paramStatus == '3') {
                $data = $data->whereIn('distribusis.status', ['2', '34']);
            }
        }
        if ($pageon == '' || $pageon == 'today' || $pageon == 'reload') {
            if (auth()->user()->roleuser_id == '2') {
                $data = $data->where('sales.parentuser_id', auth()->user()->id);
            } else if (auth()->user()->roleuser_id == '5') {
                $data = $data->where('sales.sm_id', auth()->user()->id);
            } else if (auth()->user()->roleuser_id == '6') {
                $data = $data->where('sales.um_id', auth()->user()->id);
            }
            if (auth()->user()->roleuser_id != '1') {
                $data = $data->where('sales.cabang_id', auth()->user()->cabang_id);
            }
            if ($idcampaign != '') {
                //if ($pageon != 'today') {
                $data = $data->join(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) use ($pageon, $idcampaign, $request) {
                    $join->on('distribusis.id', '=', 'a.id');
                    // if ($pageon == 'today') {
                    //     $join = $join->whereDate('distribusis.updated_at', '>=', $request->fromtanggal)
                    //         ->whereDate('distribusis.updated_at', '<=', $request->totanggal);
                    // }
                });
                //}
                $data = $data->where('fileexcels.id', $idcampaign);
                if ($pageon == 'today') {
                    $data = $data->whereDate('distribusis.updated_at', '>=', $request->fromtanggal)
                        ->whereDate('distribusis.updated_at', '<=', $request->totanggal);
                }
                if ($pageon == 'reload') {
                    $cekStatus = '3,12,13,14,16,18,19,26,27,28,37';
                    $myArray = explode(',', $cekStatus);
                    $data = $data->whereIn('distribusis.status', $myArray)
                        ->whereDate('distribusis.updated_at', '<>', $request->totanggal);
                }
            } else {
                $data = $data->whereDate('distribusis.updated_at', '>=', $request->fromtanggal)
                    ->whereDate('distribusis.updated_at', '<=', $request->totanggal);
            }
        } else {
            $myArray = explode(',', $pageon);
            $data = $data->whereIn('sales.id', $myArray)
                ->whereDate('distribusis.updated_at', '>=', $request->fromtanggal)
                ->whereDate('distribusis.updated_at', '<=', $request->totanggal);
        }
        $data = $data->without("Customer")
            ->without("User");
        //echo $data;
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('no_telp', '\'{{{substr($no_telp,-4)}}}')
            ->editColumn('updated_at', '{{{date("Y-m-d H:i:s",strtotime($updated_at));}}}')
            ->editColumn('csalesnama', '{{{$csalesnama == null ? $salesnama : $csalesnama;}}}')
            ->editColumn('updated_tgl', '{{{date("Y-m-d",strtotime($updated_at));}}}')
            ->addColumn('action', function ($data) {
                if ((auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '5' || auth()->user()->roleuser_id == '6') && $data->statusadmin == null) {
                    return view('admin.layouts.buttonActiontables')
                        ->with(['data' => $data, 'links' => 'modalEdit(\'' . encrypt($data->id) . '\')', 'type' => 'onclick']);
                } else {
                    return '';
                }
            })
            ->make(true);
    }
    public function callhistoryEdit(Request $request)
    {
        $id = decrypt($request->id);
        $validateData = [];
        $data = Distribusi::select(
            'distribusis.id',
            'distribusis.status',
            DB::raw('date_format(updated_at, "%Y-%m-%d %H:%i:%s") as editgl'),
        )
            ->firstWhere('id', $id);
        return $data;
    }
    public function callhistoryStoremodal(Request $request, Distribusi $distribusi)
    {
        $result = '';
        $checkdata = ['id' => $distribusi->id];

        if ($request->jenis != '') {
            $validateData['status'] = $request->jenis;
        }
        if ($request->tanggal != '') {
            $validateData['updated_at'] = $request->tanggal;
        }
        if (isset($distribusi->id)) {
            Distribusi::updateOrInsert($checkdata, $validateData);
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
