<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produk;
use App\Models\Distribusi;
use App\Models\Statuscall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdministratorController extends Controller
{
    //
    public function viewCalltracking(Request $request)
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
            ->where('jenis', '1');
        if (auth()->user()->cabang_id == '4') {
            $statusSelect =    $statusSelect->whereIn('id', ['15']);
            //$statusSelect =    $statusSelect->whereIn('id', ['15', '16', '34']);
        } else {
            $statusSelect =    $statusSelect->whereIn('id', ['1']);
            //$statusSelect =    $statusSelect->whereIn('id', ['1', '2', '3']);
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
            "userSelect" => $userSelect->get(),
            "statusSelect" => $statusSelect->get(),
            //"category" => User::all(),
        ]);
    }
    public function getCustomer(Request $request)
    {
        $data = Distribusi::select(
            'distribusis.*',
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
        } else {
            if (auth()->user()->roleuser_id != '1') {
                $data = $data->where('sales.cabang_id', auth()->user()->cabang_id);
            }
        }
        if ($request->produk_id != '') {
            $data = $data->where('distribusis.produk_id', $request->produk_id);
        }
        $data = $data->whereIn('distribusis.status', [$request->status])
            ->whereDate('distribusis.updated_at', '>=', $request->fromtanggal)
            ->whereDate('distribusis.updated_at', '<=', $request->totanggal)
            ->without("Customer")
            ->without("User");
        //echo $data;
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('no_telp', '\'{{{substr($no_telp,-4)}}}')
            ->editColumn('updated_at', '{{{date("Y-m-d H:i:s",strtotime($updated_at));}}}')
            ->editColumn('csalesnama', '{{{$csalesnama == null ? $salesnama : $csalesnama;}}}')
            ->editColumn('updated_tgl', '{{{date("Y-m-d",strtotime($updated_at));}}}')
            ->addColumn('action', function ($data) {
                if (auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '5' || auth()->user()->roleuser_id == '6') {
                    return view('admin.layouts.buttonActiontables')
                        ->with(['data' => $data, 'links' => 'modalEdit(\'' . encrypt($data->id) . '\')', 'type' => 'onclick']);
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
        $data = Distribusi::select(
            'distribusis.id',
            'distribusis.statusadmin',
            DB::raw('date_format(statusadmin_date, "%Y-%m-%d") as editgl'),
        )
            ->firstWhere('id', $id);
        return $data;
    }
    public function statusadminStoremodal(Request $request, Distribusi $distribusi)
    {
        $result = '';
        $checkdata = ['id' => $distribusi->id];

        if ($request->statusadmin != '') {
            $validateData['statusadmin'] = $request->statusadmin;
        }
        if ($request->statusadmin_date != '') {
            $validateData['statusadmin_date'] = $request->statusadmin_date;
        }
        if (isset($distribusi->id)) {
            Distribusi::updateOrInsert($checkdata, $validateData);
            $result = 'Data Berhasil di Update!';
        } else {
            $result = 'Data Berhasil di ditambahkan!';
        }

        return json_encode($result);
    }
}
