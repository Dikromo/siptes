<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Produk;
use App\Models\Fileexcel;
use App\Models\group_fileexcel;
use App\Models\Setupreload;
use App\Models\Statuscall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReloadController extends Controller
{
    public function viewCalltracking(Request $request)
    {
        $userSelect = User::where('status', '1');
        $statusSelect = Statuscall::where('status', '1');
        if (auth()->user()->cabang_id == '4') {
            $statusSelect =  $statusSelect
                ->where('cabang_id', auth()->user()->cabang_id);
        } else {
            $statusSelect =  $statusSelect
                ->where('cabang_id', '<>', '4');
        }
        $fileSelect = Fileexcel::where('user_id', auth()->user()->id)
            ->without('Customer')
            ->without('User')
            ->get();
        return view('admin.pages.config.reload.index', [
            'title' => 'Reload Setting',
            'active' => 'reloadsetting',
            'active_sub' => '',
            "data" => '',
            "get" => isset($request) ? $request : '',
            "fileSelect" => $fileSelect,
            "userSelect" => $userSelect->get(),
            "statusSelect" => $statusSelect->get(),
            //"category" => User::all(),
        ]);
    }
    public function ajaxRendercampaign(Request $request)
    {
        $today = date('Y-m-d');


        $cekReload = $this->cekReloadfile($request->fileexcel_id);

        if ($cekReload == '0') {
            $statusSelect = Statuscall::where('status', '1');
            if (auth()->user()->cabang_id == '4') {
                $statusSelect =  $statusSelect
                    ->where('cabang_id', auth()->user()->cabang_id);
            } else {
                $statusSelect =  $statusSelect
                    ->where('cabang_id', '<>', '4');
            }
            foreach ($statusSelect->get() as $item) {
                $validateData[] = [
                    'nama'          => '',
                    'fileexcel_id'  => $request->fileexcel_id,
                    'statuscall_id' => $item->id,
                    'status'        => 0,
                    'created_id'    => auth()->user()->id,
                    'created_at'    => now(),
                ];
            }
            Setupreload::Insert($validateData);
        }

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
            ->join('users', 'users.id', '=', 'distribusis.user_id');
        if (auth()->user()->roleuser_id != '1') {
            $lastDistribusi = $lastDistribusi->whereRaw('users.cabang_id = "' . auth()->user()->cabang_id . '"');
        }
        $lastDistribusi = $lastDistribusi->groupBy('customer_id');

        $cekReload = $this->cekReloadfile($request->fileexcel_id);


        $defaultreload = '
        DATE(distribusis.distribusi_at) <> "' . $today . '" AND
        (distribusis.status = "3" OR
        distribusis.status = "12" OR
        distribusis.status = "13" OR
        distribusis.status = "14" OR
        distribusis.status = "16" OR
        distribusis.status = "18" OR
        distribusis.status = "19" OR
        distribusis.status = "26" OR
        distribusis.status = "27" OR
        distribusis.status = "28" OR
        distribusis.status = "37") AND customers.provider <> "Tidak Ditemukan"';


        if ($cekReload != '0') {
            $setupReloaddata = Setupreload::where('fileexcel_id', $request->fileexcel_id)->where('status', '1');
            if ($setupReloaddata->count() > 0) {
                $defaultreload = 'DATE(distribusis.distribusi_at) <> "' . $today . '" AND (';
                $z = 0;
                foreach ($setupReloaddata->get() as $item) {
                    # code...
                    if ($z == 0) {
                        $defaultreload .= 'distribusis.status = "' . $item->statuscall_id . '"';
                    } else {
                        $defaultreload .= 'OR distribusis.status = "' . $item->statuscall_id . '"';
                    }
                    $z++;
                }
                $defaultreload .= ' ) AND customers.provider <> "Tidak Ditemukan"';
            }
        }

        $data = Fileexcel::select(
            'fileexcels.id',
            //'fileexcels.kode',
            DB::raw('fileexcels.kode AS kodenama'),
            'fileexcels.user_id as upload_user',
            DB::raw('(COUNT(IF(distribusis.status = "0", 1, null)) + COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, null))) AS sort_totaldata'),
            DB::raw('COUNT(customers.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status is null, 1, null)) AS total_nodistribusi'),
            DB::raw('COUNT(distribusis.id) AS total_data1'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, null)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, null)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, null)) AS total_callout'),
            DB::raw('COUNT(IF(statuscalls.jenis = "2", 1, null)) AS total_nocallout'),
            DB::raw('COUNT(IF(' . $defaultreload . '
                , 1, null)
                ) AS total_reload'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15"), 1, null)) AS total_closing'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34"), 1, null)) AS total_prospek'),
            DB::raw('COUNT(IF(customers.provider = "SIMPATI", 1, null)) AS total_simpati'),
            DB::raw('COUNT(IF(customers.provider = "INDOSAT", 1, null)) AS total_indosat'),
            DB::raw('COUNT(IF(customers.provider = "XL", 1, null)) AS total_xl'),
            DB::raw('COUNT(IF(customers.provider = "AXIS", 1, null)) AS total_axis'),
            DB::raw('COUNT(IF(customers.provider = "THREE", 1, null)) AS total_three'),
            DB::raw('COUNT(IF(customers.provider = "SMART", 1, null)) AS total_smart'),
            DB::raw('COUNT(IF(customers.provider = "TIDAK DITEMUKAN", 1, null)) AS total_noprovider'),
        )
            ->leftjoin('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                $join->on('customers.id', '=', 'a.customer_id');
            })
            ->leftjoin('distribusis', function ($join) use ($today) {
                $join->on('distribusis.id', '=', 'a.id');
                // ->where(function ($query)  use ($today, $today2, $today3) {
                //     $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                //         ->whereDate('distribusis.distribusi_at', '<=', $today);
                // });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->leftjoin('users', 'users.id', '=', 'distribusis.user_id');
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->whereIn('fileexcels.user_id', [auth()->user()->id]);
        }
        $data = $data->where('fileexcels.id', $request->fileexcel_id)
            ->orderby('sort_totaldata', 'desc')
            ->groupBy(DB::raw('1,2,3'))
            ->without("Customer")
            ->without("User");

        $data2 = Fileexcel::select(
            'statuscalls.id',
            'setupreloads.status',
            //'fileexcels.kode',
            DB::raw('statuscalls.nama AS statusnama'),
            DB::raw('COUNT(customers.id) AS total_data'),
        )
            ->leftjoin('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                $join->on('customers.id', '=', 'a.customer_id');
            })
            ->leftjoin('distribusis', function ($join) use ($today) {
                $join->on('distribusis.id', '=', 'a.id');
                // ->where(function ($query)  use ($today, $today2, $today3) {
                //     $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                //         ->whereDate('distribusis.distribusi_at', '<=', $today);
                // });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->leftjoin('setupreloads', function ($join) use ($request) {
                $join->on('setupreloads.statuscall_id', '=', 'statuscalls.id')
                    ->where('setupreloads.fileexcel_id', $request->fileexcel_id);
                // ->where(function ($query)  use ($today, $today2, $today3) {
                //     $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                //         ->whereDate('distribusis.distribusi_at', '<=', $today);
                // });
            })
            ->leftjoin('users', 'users.id', '=', 'distribusis.user_id');
        if (auth()->user()->roleuser_id != '1') {
            $data2 = $data2->whereIn('fileexcels.user_id', [auth()->user()->id]);
        }
        $data2 = $data2->where('fileexcels.id', $request->fileexcel_id)
            ->where('customers.provider', '<>', 'Tidak Ditemukan')
            ->groupBy(DB::raw('1,2,3'))
            ->without("Customer")
            ->without("User");

        $result['provider'] = $data->get();
        $result['status'] = $data2->get();
        return json_encode($result);
    }
    private function cekReloadfile($id)
    {
        $statusSelect = Setupreload::where('fileexcel_id', $id);
        return $statusSelect->count();
    }
    public function saveSetupreload(Request $request)
    {
        $checkdata = [
            'fileexcel_id' => $request->fileexcel_id,
            'statuscall_id' => $request->statuscall_id
        ];
        $status = $request->inputbox == 'YES' ? '1' : '0';
        $validateData = [
            'nama'          => '',
            'fileexcel_id'  => $request->fileexcel_id,
            'statuscall_id' => $request->statuscall_id,
            'status'        => $status,
            'created_id'    => auth()->user()->id,
            'created_at'    => now(),
        ];

        Setupreload::updateOrInsert($checkdata, $validateData);
        return json_encode('done');
    }
}
