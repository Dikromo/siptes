<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Fileexcel;
use App\Models\Distribusi;
use App\Models\Setupreload;
use Illuminate\Http\Request;
use App\Models\Log_distribusi;
use App\Models\group_fileexcel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class DistribusiController extends Controller
{
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
        return view('admin.pages.customer.distribusi_new', [
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
    public function customersDistribusifrom_new(Request $request)
    {
        $produk_id = auth()->user()->roleuser_id == '2' ? auth()->user()->produk_id : $request->produk_id;
        $tanggal = date('Y-m-d');

        if ($request->fileexcel_id != 'today') {
            if ($request->tipe == 'RELOAD') {
                $defaultreload = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                // $defaultreload = ['3', '11', '12', '13', '14', '16', '17', '18', '19', '25', '26', '27', '28', '35', '37'];

                $defaultreload = [];
                foreach ($request->fileexcel_id as $rfileexcel) {
                    $setupReloaddata = Setupreload::where('fileexcel_id', $rfileexcel)->where('status', '1');
                    if ($setupReloaddata->count() > 0) {
                        foreach ($setupReloaddata->get() as $item) {
                            # code...
                            $defaultreload[$rfileexcel][] = $item->statuscall_id;
                        }
                    } else {
                        $defaultreload[$rfileexcel] = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                    }
                }
                $lastDistribusi = DB::table('distribusis')
                    ->select('customer_id', DB::raw('MAX(id) as id'), DB::raw('COUNT(id) AS tot'))
                    ->where('produk_id', $produk_id)
                    ->groupBy('customer_id')
                    ->orderBy('tot', 'asc');
            } else {
                /// DISTRIBUSI TIPE
            }
        } else {
            //Kode Today Proses
        }

        $data = Customer::select(
            'fileexcels.id',
            'fileexcels.kode',
            DB::raw('COUNT(customers.id) AS total_data'),
            DB::raw('COUNT(IF(customers.provider = "SIMPATI", 1, null)) AS total_simpati'),
            DB::raw('COUNT(IF(customers.provider = "INDOSAT", 1, null)) AS total_indosat'),
            DB::raw('COUNT(IF(customers.provider = "XL", 1, null)) AS total_xl'),
            DB::raw('COUNT(IF(customers.provider = "AXIS", 1, null)) AS total_axis'),
            DB::raw('COUNT(IF(customers.provider = "THREE", 1, null)) AS total_three'),
            DB::raw('COUNT(IF(customers.provider = "SMART", 1, null)) AS total_smart'),
            DB::raw('COUNT(IF(customers.provider <> "SIMPATI", 1, null)) AS total_nosimpati'),
        )
            ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id');
        if ($request->tipe == 'RELOAD') {
            $data = $data->joinSub($lastDistribusi, 'a', function ($join) {
                $join->on('customers.id', '=', 'a.customer_id');
            })
                // $data =   $data->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                //     $join->on('customers.id', '=', 'a.customer_id');
                // })
                ->leftjoin('distribusis as b', 'b.id', '=', 'a.id')
                ->leftjoin('users', 'users.id', '=', 'b.user_id');
        }
        if ($request->tipe <> 'RELOAD') {
            if ($request->provider == 'NON-SIMPATI') {
                $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                    ->where('customers.provider', '<>', 'Tidak Ditemukan');
            } else if ($request->provider == 'ALL-PROVIDER') {
                $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
            } else {
                $data =   $data->where('provider', $request->provider);
            }
        }
        if (isset($request->fileexcel_id) && ($request->tipe <> 'RELOAD')) {
            $data =   $data->whereIn('customers.fileexcel_id', $request->fileexcel_id);
        }
        if ($request->tipe == 'DISTRIBUSI') {
            $data = $data->leftjoin('distribusis', function ($join) use ($produk_id, $tanggal) {
                $join->on('customers.id', '=', 'distribusis.customer_id')
                    ->where(function ($query)  use ($produk_id, $tanggal) {
                        $query->where('distribusis.produk_id', '=', $produk_id)
                            ->orwhereDate('distribusis.distribusi_at', $tanggal);
                    });
            })->where('distribusis.produk_id', null);
        }
        if ($request->tipe == 'RELOAD') {
            //dd($defaultreload);
            $i = 0;
            foreach ($request->fileexcel_id as $rfileexcel) {
                if ($i == 0) {
                    $data = $data->where(function ($query)  use ($defaultreload, $rfileexcel, $request, $tanggal, $produk_id) {
                        $query
                            ->whereDate('b.distribusi_at', '<>', $tanggal)
                            ->where('b.produk_id', $produk_id)
                            ->whereIn('b.status', $defaultreload[$rfileexcel])
                            ->where('customers.fileexcel_id', $rfileexcel);
                        if ($request->provider == 'NON-SIMPATI') {
                            $query =   $query->where('customers.provider', '<>', 'SIMPATI')
                                ->where('customers.provider', '<>', 'Tidak Ditemukan');
                        } else if ($request->provider == 'ALL-PROVIDER') {
                            $query =   $query->where('customers.provider', '<>', 'Tidak Ditemukan');
                        } else {
                            $query =   $query->where('provider', $request->provider);
                        }
                        if ($request->rbox_filter == 'sm') {
                            if (isset($request->user_id)) {
                                $datauser = User::select(DB::raw('IF((users.sm_id is not null AND users.sm_id <> "0"), users.sm_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                $query =   $query->whereNotIn('users.sm_id', $datauser);
                            }
                        } elseif ($request->rbox_filter == 'spv') {
                            if (isset($request->user_id)) {
                                $datauser = User::select(DB::raw('IF((users.parentuser_id is not null AND users.parentuser_id <> "0"), users.parentuser_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                $query =   $query->whereNotIn('users.parentuser_id', $datauser);
                            }
                        } else {
                            if (isset($request->user_id)) {
                                $query =   $query->whereNotIn('users.id', $request->user_id);
                            }
                        }
                    });
                } else {
                    $data = $data->orWhere(function ($query)  use ($defaultreload, $rfileexcel, $request, $tanggal, $produk_id) {
                        $query->whereDate('b.distribusi_at', '<>', $tanggal)
                            ->where('b.produk_id', $produk_id)
                            ->whereIn('b.status', $defaultreload[$rfileexcel])
                            ->where('customers.fileexcel_id', $rfileexcel);
                        if ($request->provider == 'NON-SIMPATI') {
                            $query =   $query->where('customers.provider', '<>', 'SIMPATI')
                                ->where('customers.provider', '<>', 'Tidak Ditemukan');
                        } else if ($request->provider == 'ALL-PROVIDER') {
                            $query =   $query->where('customers.provider', '<>', 'Tidak Ditemukan');
                        } else {
                            $query =   $query->where('provider', $request->provider);
                        }
                        if ($request->rbox_filter == 'sm') {
                            if (isset($request->user_id)) {
                                $datauser = User::select(DB::raw('IF((users.sm_id is not null AND users.sm_id <> "0"), users.sm_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                $query =   $query->whereNotIn('users.sm_id', $datauser);
                            }
                        } elseif ($request->rbox_filter == 'spv') {
                            if (isset($request->user_id)) {
                                $datauser = User::select(DB::raw('IF((users.parentuser_id is not null AND users.parentuser_id <> "0"), users.parentuser_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                $query =   $query->whereNotIn('users.parentuser_id', $datauser);
                            }
                        } else {
                            if (isset($request->user_id)) {
                                $query =   $query->whereNotIn('users.id', $request->user_id);
                            }
                        }
                    });
                }
                $i++;
                //->whereIn('b.status', $defaultreload);
            }
        }
        $data = $data->groupBy(DB::raw('1,2'));

        return DataTables::of($data->get())
            ->addIndexColumn()
            ->make(true);
    }
    public function customersDistribusiproses_new(Request $request)
    {
        // // //dd(json_decode($request->arrayKode));
        // $arrayKode = json_decode($request->arrayKode);
        // echo $arrayKode['1']->id;
        // // foreach (json_decode($request->arrayKode) as $item) {
        // //     echo $item->id . '========' . $item->limit;
        // // };
        // exit;
        $produk_id = auth()->user()->roleuser_id == '2' ? auth()->user()->produk_id : $request->produk_id;
        $tanggal = date('Y-m-d');
        $arrayKode = json_decode($request->arrayKode);
        $parammsg = '';
        $msg = '';
        $msglog = '';
        $msglog2 = '';
        $getUser = '';
        $data = '';
        $dataExec = '';

        if ($request->fileexcel_id != 'today') {
            if ($request->tipe == 'RELOAD') {
                $defaultreload = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                // $defaultreload = ['3', '11', '12', '13', '14', '16', '17', '18', '19', '25', '26', '27', '28', '35', '37'];

                $defaultreload = [];
                foreach ($request->fileexcel_id as $rfileexcel) {
                    $setupReloaddata = Setupreload::where('fileexcel_id', $rfileexcel)->where('status', '1');
                    if ($setupReloaddata->count() > 0) {
                        foreach ($setupReloaddata->get() as $item) {
                            # code...
                            $defaultreload[$rfileexcel][] = $item->statuscall_id;
                        }
                    } else {
                        $defaultreload[$rfileexcel] = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                    }
                }
                $lastDistribusi = DB::table('distribusis')
                    ->select('customer_id', DB::raw('MAX(id) as id'), DB::raw('COUNT(id) AS tot'))
                    ->where('produk_id', $produk_id)
                    ->groupBy('customer_id')
                    ->orderBy('tot', 'asc');
            } else {
                /// DISTRIBUSI TIPE
            }
        } else {
            //Kode Today Proses
        }
        foreach ($request->user_id as $user_id) {

            $msglog = '';
            $msglog2 = '';
            $data = Customer::select(
                'customers.id as customer_id',
                DB::raw('CONCAT("' . $user_id . '") as user_id'),
                DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                DB::raw('CONCAT("1") as bank_id'),
                DB::raw('CONCAT("0") as status'),
                DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                'customers.fileexcel_id',
            )
                ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id');
            if ($request->tipe == 'RELOAD') {
                $parammsg = 'Reload';
                $data = $data->joinSub($lastDistribusi, 'a', function ($join) {
                    $join->on('customers.id', '=', 'a.customer_id');
                })
                    // $data =   $data->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                    //     $join->on('customers.id', '=', 'a.customer_id');
                    // })
                    ->leftjoin('distribusis as b', 'b.id', '=', 'a.id')
                    ->leftjoin('users', 'users.id', '=', 'b.user_id');
            }
            if ($request->tipe <> 'RELOAD') {
                if ($request->provider == 'NON-SIMPATI') {
                    $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                        ->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else if ($request->provider == 'ALL-PROVIDER') {
                    $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else {
                    $data =   $data->where('provider', $request->provider);
                }
            }
            if (isset($request->fileexcel_id) && ($request->tipe <> 'RELOAD')) {
                $data =   $data->whereIn('customers.fileexcel_id', $request->fileexcel_id);
            }
            if ($request->tipe == 'DISTRIBUSI') {
                $parammsg = 'Mendistribusi';
                $data = $data->inRandomOrder()
                    ->leftjoin('distribusis', function ($join) use ($produk_id, $tanggal) {
                        $join->on('customers.id', '=', 'distribusis.customer_id')
                            ->where(function ($query)  use ($produk_id, $tanggal) {
                                $query->where('distribusis.produk_id', '=', $produk_id)
                                    ->orwhereDate('distribusis.distribusi_at', $tanggal);
                            });
                    })->where('distribusis.produk_id', null);
            }
            if ($request->tipe == 'RELOAD') {
                //dd($defaultreload);
                $i = 0;
                foreach ($request->fileexcel_id as $rfileexcel) {
                    if ($i == 0) {
                        $data = $data->where(function ($query)  use ($defaultreload, $rfileexcel, $request, $tanggal, $produk_id) {
                            $query
                                ->whereDate('b.distribusi_at', '<>', $tanggal)
                                ->where('b.produk_id', $produk_id)
                                ->whereIn('b.status', $defaultreload[$rfileexcel])
                                ->where('customers.fileexcel_id', $rfileexcel);
                            if ($request->provider == 'NON-SIMPATI') {
                                $query =   $query->where('customers.provider', '<>', 'SIMPATI')
                                    ->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else if ($request->provider == 'ALL-PROVIDER') {
                                $query =   $query->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else {
                                $query =   $query->where('provider', $request->provider);
                            }
                            if ($request->rbox_filter == 'sm') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.sm_id is not null AND users.sm_id <> "0"), users.sm_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.sm_id', $datauser);
                                }
                            } elseif ($request->rbox_filter == 'spv') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.parentuser_id is not null AND users.parentuser_id <> "0"), users.parentuser_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.parentuser_id', $datauser);
                                }
                            } else {
                                if (isset($request->user_id)) {
                                    $query =   $query->whereNotIn('users.id', $request->user_id);
                                }
                            }
                        });
                    } else {
                        $data = $data->orWhere(function ($query)  use ($defaultreload, $rfileexcel, $request, $tanggal, $produk_id) {
                            $query->whereDate('b.distribusi_at', '<>', $tanggal)
                                ->where('b.produk_id', $produk_id)
                                ->whereIn('b.status', $defaultreload[$rfileexcel])
                                ->where('customers.fileexcel_id', $rfileexcel);
                            if ($request->provider == 'NON-SIMPATI') {
                                $query =   $query->where('customers.provider', '<>', 'SIMPATI')
                                    ->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else if ($request->provider == 'ALL-PROVIDER') {
                                $query =   $query->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else {
                                $query =   $query->where('provider', $request->provider);
                            }
                            if ($request->rbox_filter == 'sm') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.sm_id is not null AND users.sm_id <> "0"), users.sm_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.sm_id', $datauser);
                                }
                            } elseif ($request->rbox_filter == 'spv') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.parentuser_id is not null AND users.parentuser_id <> "0"), users.parentuser_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.parentuser_id', $datauser);
                                }
                            } else {
                                if (isset($request->user_id)) {
                                    $query =   $query->whereNotIn('users.id', $request->user_id);
                                }
                            }
                        });
                    }
                    $i++;
                    //->whereIn('b.status', $defaultreload);
                }
            }
            $dataExec = DB::table($data, 'c')
                ->select(
                    'c.customer_id',
                    'c.user_id',
                    'c.produk_id',
                    'c.bank_id',
                    'c.status',
                    'c.distribusi_at',
                    'c.fileexcel_id',
                )
                ->addSelect(DB::raw('row_number() over(partition by `c`.`fileexcel_id` order by `c`.`customer_id`) as rn'));
            $dataExec2 = DB::table($dataExec, 'd')
                ->select(
                    'd.customer_id',
                    'd.user_id',
                    'd.produk_id',
                    'd.bank_id',
                    'd.status',
                    'd.distribusi_at',
                );
            $z = 0;
            $msglog2 = ' campaign ';
            foreach ($arrayKode as $item) {
                if ($z == 0) {
                    $msglog2 .= $item->kode;
                    $dataExec2 = $dataExec2->where(function ($query)  use ($item, $request) {
                        $totData = round(($item->limit / $request->gTotal) * $request->total);
                        $query->where('d.fileexcel_id', '=', $item->id)
                            ->where('rn', '>=', '1')
                            ->where('rn', '<=', $totData);
                        //dd($totData);
                    });
                } else {
                    $msglog2 .= ',' . $item->kode;
                    $dataExec2 = $dataExec2->orWhere(function ($query)  use ($item, $request) {
                        $totData = round(($item->limit / $request->gTotal) * $request->total);
                        $query->where('d.fileexcel_id', '=', $item->id)
                            ->where('rn', '>=', '1')
                            ->where('rn', '<=', $totData);
                        //dd($totData);
                    });
                }
                $z++;
            }
            $dataExec2 = $dataExec2->get();
            $insertData = [];
            foreach ($dataExec2 as $item) {
                # code...
                $insertData[] = [
                    'customer_id' => $item->customer_id,
                    'user_id' => $item->user_id,
                    'produk_id' => $item->produk_id,
                    'bank_id' => $item->bank_id,
                    'status' => $item->status,
                    'distribusi_at' => $item->distribusi_at,
                ];
                DB::table('customers')
                    ->where('id', $item->customer_id)
                    ->update(['status' => 1, 'updated_at' => now()]);
            }
            //$distribusiInsert = $dataExec2->toArray();
            // dd($insertData);
            // exit;
            Distribusi::insert($insertData);
            $getUser = User::firstWhere('id', $user_id)->name;
            $msg .= '
        Sukses mendistribusi data kepada <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span><br>
        ';
            $msglog = 'Sukses ' . $parammsg . ' data' . $msglog2 . ' provider ' . $request->provider . ' kepada ' . $getUser . '';

            $logInsert = [
                'tipe' => $request->tipe,
                'kode' => $msglog2,
                'provider' => $request->provider,
                'nama_sales' => $getUser,
                'deskripsi' => $msglog,
                'total' => $request->total,
                'user_id' => auth()->user()->id,
                'created_at' => now(),
            ];
            Log_distribusi::create($logInsert);
        }
        return back()->with(['msg' => $msg]);
    }
    public function customersDistribusiproses_new1(Request $request)
    {
        // // //dd(json_decode($request->arrayKode));
        // $arrayKode = json_decode($request->arrayKode);
        // echo $arrayKode['1']->id;
        // // foreach (json_decode($request->arrayKode) as $item) {
        // //     echo $item->id . '========' . $item->limit;
        // // };
        // exit;
        $produk_id = auth()->user()->roleuser_id == '2' ? auth()->user()->produk_id : $request->produk_id;
        $tanggal = date('Y-m-d');
        $arrayKode = json_decode($request->arrayKode);
        $parammsg = '';
        $msg = '';
        $msglog = '';
        $msglog2 = '';
        $getUser = '';
        $data = '';
        $dataExec = '';
        $gTotal = $request->gTotal;

        if ($request->fileexcel_id != 'today') {
            if ($request->tipe == 'RELOAD') {
                $defaultreload = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                // $defaultreload = ['3', '11', '12', '13', '14', '16', '17', '18', '19', '25', '26', '27', '28', '35', '37'];

                $defaultreload = [];
                foreach ($request->fileexcel_id as $rfileexcel) {
                    $setupReloaddata = Setupreload::where('fileexcel_id', $rfileexcel)->where('status', '1');
                    if ($setupReloaddata->count() > 0) {
                        foreach ($setupReloaddata->get() as $item) {
                            # code...
                            $defaultreload[$rfileexcel][] = $item->statuscall_id;
                        }
                    } else {
                        $defaultreload[$rfileexcel] = ['3', '12', '13', '14', '16', '18', '19', '26', '27', '28', '37'];
                    }
                }
                $lastDistribusi = DB::table('distribusis')
                    ->select('customer_id', DB::raw('MAX(id) as id'), DB::raw('COUNT(id) AS tot'))
                    ->where('produk_id', $produk_id)
                    ->groupBy('customer_id')
                    ->orderBy('tot', 'asc');
            } else {
                /// DISTRIBUSI TIPE
            }
        } else {
            //Kode Today Proses
        }
        foreach ($request->user_id as $user_id) {

            $msglog = '';
            $msglog2 = '';
            $data = Customer::select(
                'customers.id as customer_id',
                DB::raw('CONCAT("' . $user_id . '") as user_id'),
                DB::raw('CONCAT("' . $produk_id . '") as produk_id'),
                DB::raw('CONCAT("1") as bank_id'),
                DB::raw('CONCAT("0") as status'),
                DB::raw('CURRENT_TIMESTAMP() as distribusi_at'),
                'customers.fileexcel_id',
            )
                ->join('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id');
            if ($request->tipe == 'RELOAD') {
                $parammsg = 'Reload';
                $data = $data->joinSub($lastDistribusi, 'a', function ($join) {
                    $join->on('customers.id', '=', 'a.customer_id');
                })
                    // $data =   $data->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                    //     $join->on('customers.id', '=', 'a.customer_id');
                    // })
                    ->leftjoin('distribusis as b', 'b.id', '=', 'a.id')
                    ->leftjoin('users', 'users.id', '=', 'b.user_id');
            }
            if ($request->tipe <> 'RELOAD') {
                if ($request->provider == 'NON-SIMPATI') {
                    $data =   $data->where('customers.provider', '<>', 'SIMPATI')
                        ->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else if ($request->provider == 'ALL-PROVIDER') {
                    $data =   $data->where('customers.provider', '<>', 'Tidak Ditemukan');
                } else {
                    $data =   $data->where('provider', $request->provider);
                }
            }
            if (isset($request->fileexcel_id) && ($request->tipe <> 'RELOAD')) {
                $data =   $data->whereIn('customers.fileexcel_id', $request->fileexcel_id);
            }
            if ($request->tipe == 'DISTRIBUSI') {
                $parammsg = 'Mendistribusi';
                $data = $data->inRandomOrder()
                    ->leftjoin('distribusis', function ($join) use ($produk_id, $tanggal) {
                        $join->on('customers.id', '=', 'distribusis.customer_id')
                            ->where(function ($query)  use ($produk_id, $tanggal) {
                                $query->where('distribusis.produk_id', '=', $produk_id)
                                    ->orwhereDate('distribusis.distribusi_at', $tanggal);
                            });
                    })->where('distribusis.produk_id', null);
            }
            if ($request->tipe == 'RELOAD') {
                //dd($defaultreload);
                $i = 0;
                foreach ($request->fileexcel_id as $rfileexcel) {
                    if ($i == 0) {
                        $data = $data->where(function ($query)  use ($defaultreload, $rfileexcel, $request, $tanggal, $produk_id) {
                            $query
                                ->whereDate('b.distribusi_at', '<>', $tanggal)
                                ->where('b.produk_id', $produk_id)
                                ->whereIn('b.status', $defaultreload[$rfileexcel])
                                ->where('customers.fileexcel_id', $rfileexcel);
                            if ($request->provider == 'NON-SIMPATI') {
                                $query =   $query->where('customers.provider', '<>', 'SIMPATI')
                                    ->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else if ($request->provider == 'ALL-PROVIDER') {
                                $query =   $query->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else {
                                $query =   $query->where('provider', $request->provider);
                            }
                            if ($request->rbox_filter == 'sm') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.sm_id is not null AND users.sm_id <> "0"), users.sm_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.sm_id', $datauser);
                                }
                            } elseif ($request->rbox_filter == 'spv') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.parentuser_id is not null AND users.parentuser_id <> "0"), users.parentuser_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.parentuser_id', $datauser);
                                }
                            } else {
                                if (isset($request->user_id)) {
                                    $query =   $query->whereNotIn('users.id', $request->user_id);
                                }
                            }
                        });
                    } else {
                        $data = $data->orWhere(function ($query)  use ($defaultreload, $rfileexcel, $request, $tanggal, $produk_id) {
                            $query->whereDate('b.distribusi_at', '<>', $tanggal)
                                ->where('b.produk_id', $produk_id)
                                ->whereIn('b.status', $defaultreload[$rfileexcel])
                                ->where('customers.fileexcel_id', $rfileexcel);
                            if ($request->provider == 'NON-SIMPATI') {
                                $query =   $query->where('customers.provider', '<>', 'SIMPATI')
                                    ->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else if ($request->provider == 'ALL-PROVIDER') {
                                $query =   $query->where('customers.provider', '<>', 'Tidak Ditemukan');
                            } else {
                                $query =   $query->where('provider', $request->provider);
                            }
                            if ($request->rbox_filter == 'sm') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.sm_id is not null AND users.sm_id <> "0"), users.sm_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.sm_id', $datauser);
                                }
                            } elseif ($request->rbox_filter == 'spv') {
                                if (isset($request->user_id)) {
                                    $datauser = User::select(DB::raw('IF((users.parentuser_id is not null AND users.parentuser_id <> "0"), users.parentuser_id, users.id) as sm_id'))->whereIn('id', $request->user_id)->groupBy(DB::raw('1'))->get();
                                    $query =   $query->whereNotIn('users.parentuser_id', $datauser);
                                }
                            } else {
                                if (isset($request->user_id)) {
                                    $query =   $query->whereNotIn('users.id', $request->user_id);
                                }
                            }
                        });
                    }
                    $i++;
                    //->whereIn('b.status', $defaultreload);
                }
            }
            $dataExec = DB::table($data, 'c')
                ->select(
                    'c.customer_id',
                    'c.user_id',
                    'c.produk_id',
                    'c.bank_id',
                    'c.status',
                    'c.distribusi_at',
                    'c.fileexcel_id',
                )
                ->addSelect(DB::raw('row_number() over(partition by `c`.`fileexcel_id` order by `c`.`customer_id`) as rn'));
            $dataExec2 = DB::table($dataExec, 'd')
                ->select(
                    'd.customer_id',
                    'd.user_id',
                    'd.produk_id',
                    'd.bank_id',
                    'd.status',
                    'd.distribusi_at',
                );
            $z = 0;
            $msglog2 = ' campaign ';
            foreach ($arrayKode as $item) {
                //echo $gTotal . '==' . $arrayKode[$z]->kode . '-----------' . $arrayKode[$z]->limit . 'aaaaa<br>';
                if ($z == 0) {
                    $msglog2 .= $item->kode;
                    $totData = round(($item->limit / $gTotal) * $request->total);
                    $dataExec2 = $dataExec2->where(function ($query)  use ($item, $totData) {
                        $query->where('d.fileexcel_id', '=', $item->id)
                            ->where('rn', '>=', '1')
                            ->where('rn', '<=', $totData);
                        //dd($totData);
                    });
                    $gTotal = $gTotal - $totData;
                    $arrayKode[$z]->limit = ($item->limit - $totData) < 0 ? 0 : $item->limit - $totData;
                } else {
                    $msglog2 .= ',' . $item->kode;
                    $totData = round(($item->limit / $gTotal) * $request->total);
                    $dataExec2 = $dataExec2->orWhere(function ($query)  use ($item, $totData) {
                        $query->where('d.fileexcel_id', '=', $item->id)
                            ->where('rn', '>=', '1')
                            ->where('rn', '<=', $totData);
                        //dd($totData);
                    });
                    $gTotal = $gTotal - $totData;
                    $arrayKode[$z]->limit = ($item->limit - $totData) < 0 ? 0 : $item->limit - $totData;
                }
                //echo $gTotal . '==' . $arrayKode[$z]->limit . '||||||||||||||' . $totData . '<br>';
                $z++;
            }
            $dataExec2 = $dataExec2->get();
            $insertData = [];
            foreach ($dataExec2 as $item) {
                # code...
                $insertData[] = [
                    'customer_id' => $item->customer_id,
                    'user_id' => $item->user_id,
                    'produk_id' => $item->produk_id,
                    'bank_id' => $item->bank_id,
                    'status' => $item->status,
                    'distribusi_at' => $item->distribusi_at,
                ];
                DB::table('customers')
                    ->where('id', $item->customer_id)
                    ->update(['status' => 1, 'updated_at' => now()]);
            }
            //$distribusiInsert = $dataExec2->toArray();
            // dd($insertData);
            // exit;
            Distribusi::insert($insertData);
            $getUser = User::firstWhere('id', $user_id)->name;
            $msg .= '
        Sukses mendistribusi data kepada <span style="color:#00ff00;font-weight:600;">' . $getUser . '</span><br>
        ';
            $msglog = 'Sukses ' . $parammsg . ' data' . $msglog2 . ' provider ' . $request->provider . ' kepada ' . $getUser . '';

            $logInsert = [
                'tipe' => $request->tipe,
                'kode' => $msglog2,
                'provider' => $request->provider,
                'nama_sales' => $getUser,
                'deskripsi' => $msglog,
                'total' => $request->total,
                'user_id' => auth()->user()->id,
                'created_at' => now(),
            ];
            Log_distribusi::create($logInsert);
        }
        return back()->with(['msg' => $msg]);
    }
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
                    ->leftjoin('customers', 'customers.id', '=', 'a.customer_id')
                    ->leftjoin('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
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

            $today = date('Y-m-d', strtotime('-31 days', strtotime(date('Y-m-d'))));
            $data = Distribusi::select(
                'customers.nama',
                'customers.no_telp',
                'customers.provider',
                'fileexcels.kode',
            )
                ->leftjoin('customers', 'customers.id', '=', 'distribusis.customer_id')
                ->leftjoin('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
                ->where('distribusis.user_id', auth()->user()->id)
                ->where('distribusis.status', '0')
                //->whereDate('distribusis.distribusi_at', '>=', $today)
                // ->where(function ($query) {
                //     $query->where('distribusis.status', '0')
                //         ->orWhere('distribusis.status', null);
                // })
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
        $today = date('Y-m-d', strtotime('-31 days', strtotime(date('Y-m-d'))));
        $data = Distribusi::select(
            'customers.nama',
            'customers.no_telp',
            'customers.provider',
            'fileexcels.kode',
        )
            ->leftjoin('customers', 'customers.id', '=', 'distribusis.customer_id')
            ->leftjoin('fileexcels', 'fileexcels.id', '=', 'customers.fileexcel_id')
            ->whereIn('distribusis.user_id',  $request->user_id)
            //->whereDate('distribusis.distribusi_at', '>=', $today)
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
                        $msglogFileexcel = $getfileexcel->nama . ' ID GROUP ' . $request->group_fileexcels_id;
                    } else {
                        $getfileexcel = Fileexcel::firstWhere('id', $request->fileexcel_id);
                        $msglog2 = ' campaign ' . $getfileexcel->kode;
                        $msglogFileexcel = $getfileexcel->kode . ' ID Campaign ' . $request->fileexcel_id;
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

                if ($request->group_fileexcels_id != '') {
                    $getfileexcel = group_fileexcel::firstWhere('id', $request->group_fileexcels_id);
                    $msglog2 = ' Group campaign ' . $getfileexcel->nama;
                    $msglogFileexcel = $getfileexcel->nama . ' ID Campaign ' . $request->group_fileexcels_id;
                } else {
                    $getfileexcel = Fileexcel::firstWhere('id', $request->fileexcel_id);
                    $fileKode = $getfileexcel != null ? $getfileexcel->kode : '';
                    $msglog2 = ' campaign ' . $fileKode;
                    $msglogFileexcel = $fileKode . ' ID Campaign ' . $request->fileexcel_id;
                }

                $getUser = User::firstWhere('id', $user_id)->name;
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
                    $msglogFileexcel = $getfileexcel->nama . ' ID Campaign ' . $request->group_fileexcels_id;
                } else {
                    $getfileexcel = Fileexcel::firstWhere('id', $request->fileexcel_id);
                    $msglog2 = ' campaign ' . $getfileexcel->kode;
                    $msglogFileexcel = $getfileexcel->kode . ' ID Campaign ' . $request->fileexcel_id;
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
}
