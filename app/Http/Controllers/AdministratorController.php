<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produk;
use App\Models\Distribusi;
use App\Models\Statuscall;
use App\Models\Submition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdministratorController extends Controller
{
    //
    public function viewCalltracking(Request $request)
    {
        //$userSelect = User::where('status', '1');
        $salesSelect =  User::where('status', '1')->where('roleuser_id', '3');
        $leaderSelect =  User::where('status', '1')->where('roleuser_id', '2');
        $managerSelect =  User::where('status', '1')->where('roleuser_id', '5');
        // if (auth()->user()->roleuser_id == '2') {
        //     $userSelect = $userSelect->where('parentuser_id', auth()->user()->id)
        //         ->where(function ($query) {
        //             $query->where('roleuser_id', '2')
        //                 ->orWhere('roleuser_id', '3');
        //         });
        // } else if (auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '6') {
        //     $userSelect = $userSelect->where('cabang_id', auth()->user()->cabang_id)
        //         ->where(function ($query) {
        //             $query->where('roleuser_id', '2')
        //                 ->orWhere('roleuser_id', '3');
        //         });
        // } else if (auth()->user()->roleuser_id == '5') {
        //     $userSelect = $userSelect->where('sm_id', auth()->user()->id)
        //         ->where(function ($query) {
        //             $query->where('roleuser_id', '2')
        //                 ->orWhere('roleuser_id', '3');
        //         });
        // } else {
        //     $userSelect = $userSelect
        //         ->where(function ($query) {
        //             $query->where('roleuser_id', '2')
        //                 ->orWhere('roleuser_id', '3');
        //         });
        // }
        $statusSelect = Statuscall::where('status', '1')
            ->where('jenis', '1');
        if (auth()->user()->roleuser_id != '1') {
            $salesSelect =  $salesSelect->where('cabang_id', auth()->user()->cabang_id);
            $leaderSelect =  $leaderSelect->where('cabang_id', auth()->user()->cabang_id);
            $managerSelect =  $managerSelect->where('cabang_id', auth()->user()->cabang_id);

            if (auth()->user()->roleuser_id == '2') {
                $salesSelect =  $salesSelect->where('parentuser_id', auth()->user()->id);
                $leaderSelect =  $leaderSelect->where('id', auth()->user()->id);
                $managerSelect =  $managerSelect->where('id', auth()->user()->sm_id);
            }

            if (auth()->user()->roleuser_id == '5') {
                $salesSelect =  $salesSelect->where('sm_id', auth()->user()->id);
                $leaderSelect =  $leaderSelect->where('sm_id', auth()->user()->id);
                $managerSelect =  $managerSelect->where('id', auth()->user()->id);
            }

            if (auth()->user()->roleuser_id == '6') {
                $salesSelect =  $salesSelect->where('um_id', auth()->user()->id);
                $leaderSelect =  $leaderSelect->where('um_id', auth()->user()->id);
                $managerSelect =  $managerSelect->where('um_id', auth()->user()->id);
            }

            if (auth()->user()->cabang_id == '4') {
                $statusSelect =    $statusSelect->whereIn('id', ['15']);
                //$statusSelect =    $statusSelect->whereIn('id', ['15', '16', '34']);
            } else {
                $statusSelect =    $statusSelect->whereIn('id', ['1']);
                //$statusSelect =    $statusSelect->whereIn('id', ['1', '2', '3']);
            }
        } else {
            $statusSelect =    $statusSelect->whereIn('id', ['1', '15']);
        }
        $produkSelect = Produk::where('status', '1')
            ->get();
        return view('admin.pages.administrator.index', [
            'title' => 'Update Nasabah',
            'active' => 'updatenasabah',
            'active_sub' => '',
            "data" => '',
            "get" => isset($request) ? $request : '',
            "produkSelect" => $produkSelect,
            "salesSelect" => $salesSelect->get(),
            "leaderSelect" => $leaderSelect->get(),
            "managerSelect" => $managerSelect->get(),
            "statusSelect" => $statusSelect->get(),
            //"category" => User::all(),
        ]);
    }
    public function getCustomer(Request $request)
    {
        $lastSubmition = DB::table('submitions')
            ->select('distribusis_id', DB::raw('MAX(id) as id'), DB::raw('COUNT(id) AS tot'))
            ->groupBy('distribusis_id')
            ->orderBy('tot', 'asc');
        $data = Distribusi::select(
            'submitions.*',
            'distribusis.deskripsi',
            'distribusis.updated_at as distribusis_updated_at',
            'customers.nama as nama',
            'customers.no_telp as no_telp',
            'customers.provider as provider',
            DB::raw('UPPER(cabangs.nama) as cabangnama'),
            DB::raw('UPPER(sales.name) as salesnama'),
            DB::raw('UPPER(IF(parentuser.name is not null, parentuser.name, sales.name)) as spvnama'),
            DB::raw('UPPER(IF(parentuser.nickname is not null, parentuser.nickname, sales.nickname)) as spvnickname'),
            DB::raw('UPPER(sm.name) as smname'),
            DB::raw('UPPER(sm.nickname) as smnickname'),
            DB::raw('CONCAT(sales.name," (",parentuser.name,") ") AS csalesnama'),
            'statuscalls.nama as statustext',
            'subproduks.nama as subproduktext',
            'fileexcels.kode as kode',
            DB::raw('timediff(distribusis.updated_at,distribusis.call_time) as selisih')
        )
            ->leftjoin(DB::raw('(' . $lastSubmition->toSql() . ') as a'), function ($join) {
                $join->on('distribusis.id', '=', 'a.distribusis_id');
            })
            ->leftjoin('submitions', 'submitions.id', '=', 'a.id')
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
        if ($request->user_id != '') {
            $data = $data->where('sales.id', $request->user_id);
        }
        if ($request->parentuser_id != '') {
            $data = $data->where(function ($query) use ($request) {
                $query->where('sales.id', $request->parentuser_id)
                    ->orWhere('parentuser.id', $request->parentuser_id);
            });
        }
        if ($request->sm_id != '') {
            $data = $data->where('sm.id', $request->sm_id);
        }
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->where('sales.cabang_id', auth()->user()->cabang_id);
        }
        if ($request->produk_id != '') {
            $data = $data->where('distribusis.produk_id', $request->produk_id);
        }
        if ($request->decision_status == '') {
            $data = $data->whereNull('submitions.id');
        } else {
            $data = $data->where('submitions.statusbank', $request->decision_status);
        }
        $data = $data
            ->whereIn('distribusis.status', [$request->status])
            ->whereDate('distribusis.updated_at', '>=', $request->fromtanggal)
            ->whereDate('distribusis.updated_at', '<=', $request->totanggal)
            ->without("Customer")
            ->without("User");
        //echo $data;
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('no_telp', '\'{{{substr($no_telp,-4)}}}')
            ->editColumn('namaktp', '{{{$namaktp == null ? strtoupper($nama) : strtoupper($namaktp);}}}')
            ->editColumn('updated_at', '{{{date("Y-m-d H:i:s",strtotime($distribusis_updated_at));}}}')
            ->editColumn('csalesnama', '{{{$csalesnama == null ? $salesnama : $csalesnama;}}}')
            ->editColumn('updated_tgl', '{{{date("Y-m-d",strtotime($distribusis_updated_at));}}}')
            ->addColumn('action', function ($data) {
                if (auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '2' || auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '5' || auth()->user()->roleuser_id == '6') {
                    return view('admin.layouts.buttonActiontables')
                        ->with(['data' => $data, 'links' => 'modalEdit(\'' . encrypt($data->distribusis_id) . '\')', 'type' => 'onclick']);
                } else {
                    return '';
                }
            })
            ->make(true);
    }
    public function statusadminEdit(Request $request)
    {
        $id = decrypt($request->id);
        $validateData = [];
        $lastSubmition = DB::table('submitions')
            ->select('distribusis_id', DB::raw('MAX(id) as id'), DB::raw('COUNT(id) AS tot'))
            ->groupBy('distribusis_id')
            ->orderBy('tot', 'asc');
        $data = Distribusi::select(
            'distribusis.id',
            'distribusis.produk_id',
            'distribusis.subproduk_id',
            'submitions.statusadmin',
            'submitions.remarksadmin',
            DB::raw('date_format(submitions.statusadmin_date, "%Y-%m-%d") as admintgl'),
            'submitions.statusbank',
            'submitions.remarksbank',
            DB::raw('date_format(submitions.statusbank_date, "%Y-%m-%d") as banktgl'),
            'submitions.temp_limit',
            'submitions.disburse_limit',
        )
            ->leftjoin(DB::raw('(' . $lastSubmition->toSql() . ') as a'), function ($join) {
                $join->on('distribusis.id', '=', 'a.distribusis_id');
            })
            ->leftjoin('submitions', 'submitions.id', '=', 'a.id')
            ->firstWhere('distribusis.id', $id);
        return $data;
    }
    public function statusadminStoremodal(Request $request, Distribusi $distribusi)
    {
        $result = '';
        $checkdata = [
            'distribusis_id' => $distribusi->id,
            'created_id' => auth()->user()->id
        ];
        if ($distribusi->id != '') {
            $validateData['distribusis_id'] = $distribusi->id;
        }
        if ($request->statusadmin != '') {
            $validateData['statusadmin'] = $request->statusadmin;
        }
        if ($request->statusadmin_date != '') {
            $validateData['statusadmin_date'] = $request->statusadmin_date;
        }
        if ($request->remarksadmin != '') {
            $validateData['remarksadmin'] = $request->remarksadmin;
        }
        if ($request->statusbank != '') {
            $validateData['statusbank'] = $request->statusbank;
        }
        if ($request->statusbank_date != '') {
            $validateData['statusbank_date'] = $request->statusbank_date;
        }
        if ($request->remarksbank != '') {
            $validateData['remarksbank'] = $request->remarksbank;
        }
        if ($request->temp_limit != '') {
            $validateData['temp_limit'] = $request->temp_limit;
        }
        if ($request->disburse_limit != '') {
            $validateData['disburse_limit'] = $request->disburse_limit;
        }

        $validateData['created_id'] = auth()->user()->id;
        $validateData['created_at'] =  now();
        $validateData['updated_at'] =  now();
        if (isset($distribusi->id)) {
            Submition::Insert($validateData);
            //Submition::updateOrInsert($checkdata, $validateData);
            $result = 'Data Berhasil di Update!';
        } else {
            $result = 'Data Berhasil di ditambahkan!';
        }

        return json_encode($result);
    }
}
