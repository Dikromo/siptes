<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Fileexcel;
use Illuminate\Http\Request;
use App\Models\group_fileexcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CampaignController extends Controller
{
    public function index()
    {
        return view(
            'admin.pages.campaign.index',
            [
                'title' => 'Campaign',
                'active' => 'campaign',
                'active_sub' => '',
                'data' => ''
            ]
        );
    }
    public function dataTables()
    {
        $data = Fileexcel::select(
            'fileexcels.id',
            'fileexcels.kode',
            DB::raw('COUNT(customers.id) AS total_data'),
            DB::raw('COUNT(IF(customers.provider = "SIMPATI", 1, NULL)) AS total_simpati'),
            DB::raw('COUNT(IF(customers.provider = "INDOSAT", 1, NULL)) AS total_indosat'),
            DB::raw('COUNT(IF(customers.provider = "XL", 1, NULL)) AS total_xl'),
            DB::raw('COUNT(IF(customers.provider = "AXIS", 1, NULL)) AS total_axis'),
            DB::raw('COUNT(IF(customers.provider = "THREE", 1, NULL)) AS total_three'),
            DB::raw('COUNT(IF(customers.provider = "SMARTFREN", 1, NULL)) AS total_smartfren'),
            DB::raw('COUNT(IF(customers.provider = "Tidak Ditemukan", 1, NULL)) AS total_tdk'),
        )
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->join('users', 'users.id', '=', 'fileexcels.user_id');
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->where('users.cabang_id', auth()->user()->cabang_id);
        }
        $data = $data->groupBy(DB::raw('1,2'))
            ->orderby('fileexcels.id', 'desc')
            ->without("Customer")
            ->without("User");
        return DataTables::of($data->get())
            ->addIndexColumn()
            // ->addColumn('statusText', function ($data) {
            //     switch ($data->status) {
            //         case '1':
            //             $statusText = 'Active';
            //             break;
            //         case '2':
            //             $statusText = 'Not Active';
            //             break;
            //         default:
            //             $statusText = 'Not Active';
            //             break;
            //     }
            //     return $statusText;
            // })
            // ->addColumn('action', function ($data) {
            //     return view('admin.layouts.buttonActiontables')
            //         ->with(['data' => $data, 'links' => 'mutasi', 'type' => 'all']);
            // })
            ->make(true);
    }

    public function indexGroup()
    {
        return view(
            'admin.pages.campaign.groupcampaign',
            [
                'title' => 'Campaign Group',
                'active' => 'campaigngroup',
                'active_sub' => '',
                'data' => ''
            ]
        );
    }
    public function groupFormadd()
    {
        return view('admin.pages.campaign.formgroup', [
            'title' => 'Campaign Group',
            'active' => 'campaigngroup',
            'active_sub' => '',
            "data" => '',
        ]);
    }
    public function groupFormedit($id)
    {
        $id = decrypt($id);
        $mutasi = group_fileexcel::firstWhere('id', $id);
        return view('admin.pages.campaign.formgroup', [
            'title' => 'Campaign Group',
            'active' => 'campaigngroup',
            'active_sub' => '',
            "data" => $mutasi,
        ]);
    }
    public function dataTablesgroup()
    {
        $today = date('Y-m-d');

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

        $data = group_fileexcel::select(
            'group_fileexcels.id',
            //'fileexcels.kode',
            DB::raw('group_fileexcels.nama AS groupnama'),
            'fileexcels.user_id as upload_user',
            DB::raw('(COUNT(IF(distribusis.status = "0", 1, null)) + COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, null))) AS sort_totaldata'),
            DB::raw('COUNT(customers.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status is null, 1, null)) AS total_nodistribusi'),
            DB::raw('COUNT(distribusis.id) AS total_data1'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, null)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, null)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, null)) AS total_callout'),
            DB::raw('COUNT(IF(statuscalls.jenis = "2", 1, null)) AS total_nocallout'),
            DB::raw('COUNT(IF(
                DATE(distribusis.distribusi_at) <> "' . $today . '" AND
                (distribusis.status = "3" OR
                distribusis.status = "11" OR
                distribusis.status = "12" OR
                distribusis.status = "13" OR
                distribusis.status = "14" OR
                distribusis.status = "16" OR
                distribusis.status = "17" OR
                distribusis.status = "18" OR
                distribusis.status = "19" OR
                distribusis.status = "24" OR
                distribusis.status = "25" OR
                distribusis.status = "26" OR
                distribusis.status = "27" OR
                distribusis.status = "28" OR
                distribusis.status = "35" OR
                distribusis.status = "37")
                , 1, null)
                ) AS total_reload'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15"), 1, null)) AS total_closing'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34"), 1, null)) AS total_prospek'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, null)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, null)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today . '" AND DATE(distribusis.updated_at) = "' . $today . '", 1, null)) AS total_call_distoday'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, null)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, null)) AS total_callout_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "2" AND DATE(distribusis.updated_at) = "' . $today . '", 1, null)) AS total_nocallout_today'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today . '", 1, null)) AS total_closing_today'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today . '", 1, null)) AS total_prospek_today'),
        )
            ->leftjoin('fileexcels', 'group_fileexcels.id', '=', 'fileexcels.group_id')
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
        $data = $data->orderby('sort_totaldata', 'desc')
            ->groupBy(DB::raw('1,2,3'))
            ->without("Customer");

        // $data = group_fileexcel::select(
        //     'group_fileexcels.id',
        //     'group_fileexcels.nama',
        //     DB::raw('GROUP_CONCAT(
        //         DISTINCT fileexcels.kode) grouplist'),
        // )
        //     ->leftjoin('fileexcels', 'fileexcels.group_id', '=', 'group_fileexcels.id')
        //     ->join('users', 'users.id', '=', 'group_fileexcels.created_id');
        // //if (auth()->user()->roleuser_id != '1') {
        // $data = $data->where('group_fileexcels.created_id', auth()->user()->id)
        //     ->groupBy(DB::raw('1,2'));
        // //}
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('all1', function ($data) use ($today) {
                $vToday = '<span style="color:#009b9b"><span title="total data">' . $data->total_data . '</span>';
                $vToday .= ' ( ';
                $vToday .= '<span style="color:#eb7904" title="total data terdistribusi">' . $data->total_data1  . '</span>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0424" title="total belum terdistribusi">' . $data->total_nodistribusi . '</span>';
                $vToday .= ' )';
                if ($data->upload_user == auth()->user()->id) {
                    $vToday .= ' ( ';
                    $vToday .= '<a href="/customer/callhistory?id=&param=&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '&pageon=reload" target="_blank"><span style="color:#eb7904" title="total reload">' . $data->total_reload . '</span></a>';
                    $vToday .= ' ) ';
                }
                return $vToday;
            })

            ->addColumn('all2', function ($data) use ($today) {
                // $persendis =  ($data->total_data == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_data1 / $data->total_data) * 100) . '%';
                // $persennodis =  ($data->total_data == '0' && $data->total_nodistribusi == '0') ? '0' : round(($data->total_nodistribusi / $data->total_data) * 100) . '%';
                // $persencall =  ($data->total_call == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_call / $data->total_data1) * 100) . '%';
                // $persennocall =  ($data->total_nocall == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_nocall / $data->total_data1) * 100) . '%';
                // $persencallout =  ($data->total_callout == '0' && $data->total_call == '0') ? '0' : round(($data->total_callout / $data->total_call) * 100) . '%';
                // $persennocallout =  ($data->total_nocallout == '0' && $data->total_call == '0') ? '0' : round(($data->total_nocallout / $data->total_call) * 100) . '%';
                // $persenprospek =  ($data->total_prospek == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_prospek / $data->total_data1) * 100) . '%';
                // $persenclosing =  ($data->total_closing == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_closing / $data->total_data1) * 100) . '%';

                $vToday = ' ( ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('0') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#eb7904" title="total telepon">' . $data->total_call . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0424;cursor: pointer;" title="total belum telepon" onclick="tarikData(\'' . encrypt($data->id) . '\',\'' . $data->kode . '\')">' . $data->total_nocall . '</span>';
                $vToday .= ' ) ';

                return $vToday;
            })
            ->addColumn('all3', function ($data) use ($today) {
                // $persendis =  ($data->total_data == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_data1 / $data->total_data) * 100) . '%';
                // $persennodis =  ($data->total_data == '0' && $data->total_nodistribusi == '0') ? '0' : round(($data->total_nodistribusi / $data->total_data) * 100) . '%';
                // $persencall =  ($data->total_call == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_call / $data->total_data1) * 100) . '%';
                // $persennocall =  ($data->total_nocall == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_nocall / $data->total_data1) * 100) . '%';
                $persencallout =  ($data->total_callout == '0' && $data->total_call == '0') ? '0' : round(($data->total_callout / $data->total_call) * 100) . '%';
                $persennocallout =  ($data->total_nocallout == '0' && $data->total_call == '0') ? '0' : round(($data->total_nocallout / $data->total_call) * 100) . '%';
                // $persenprospek =  ($data->total_prospek == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_prospek / $data->total_data1) * 100) . '%';
                // $persenclosing =  ($data->total_closing == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_closing / $data->total_data1) * 100) . '%';

                $vToday = ' ( ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('1') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#009b05" title="total contact">' . $data->total_callout . '(' . $persencallout . ')' . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('4') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#eb0424" title="total not contact">' . $data->total_nocallout . '(' . $persennocallout . ')' . '</span></a>';
                $vToday .= ' ) ';

                return $vToday;
            })
            ->addColumn('all4', function ($data) use ($today) {
                // $persendis =  ($data->total_data == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_data1 / $data->total_data) * 100) . '%';
                // $persennodis =  ($data->total_data == '0' && $data->total_nodistribusi == '0') ? '0' : round(($data->total_nodistribusi / $data->total_data) * 100) . '%';
                // $persencall =  ($data->total_call == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_call / $data->total_data1) * 100) . '%';
                // $persennocall =  ($data->total_nocall == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_nocall / $data->total_data1) * 100) . '%';
                // $persencallout =  ($data->total_callout == '0' && $data->total_call == '0') ? '0' : round(($data->total_callout / $data->total_call) * 100) . '%';
                // $persennocallout =  ($data->total_nocallout == '0' && $data->total_call == '0') ? '0' : round(($data->total_nocallout / $data->total_call) * 100) . '%';
                // $persenprospek =  ($data->total_prospek == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_prospek / $data->total_data1) * 100) . '%';
                // $persenclosing =  ($data->total_closing == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_closing / $data->total_data1) * 100) . '%';

                $vToday = '( ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('3') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#eb7904;font-weight: 600;" title="total prospek">' . $data->total_prospek . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('2') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#009b05;font-weight: 600;" title="total closing">' . $data->total_closing . '</span></a>';
                $vToday .= ' ) ';
                return $vToday;
            })
            ->addColumn('today', function ($data) use ($today) {
                $vtdt = $data->total_nocall + $data->total_call_today;
                $vsisahkemarin = $vtdt - $data->total_call_distoday - $data->total_nocall_today;
                $vdatatoday = $vtdt - ($vtdt - $data->total_call_distoday - $data->total_nocall_today);
                // $persennodis =  ($vtdt == '0' && $vsisahkemarin == '0') ? '0' : round(($vsisahkemarin / $vtdt) * 100) . '%';
                // $persendis =  ($vtdt == '0' && $vdatatoday == '0') ? '0' : round(($vdatatoday / $vtdt) * 100) . '%';
                // $persencall =  ($data->total_call_today == '0' && $vtdt == '0') ? '0' : round(($data->total_call_today / $vtdt) * 100) . '%';
                // $persennocall =  ($data->total_nocall == '0' && $vtdt == '0') ? '0' : round(($data->total_nocall / $vtdt) * 100) . '%';
                $persencallout =  ($data->total_callout_today == '0' && $data->total_call_today == '0') ? '0' : round(($data->total_callout_today / $data->total_call_today) * 100) . '%';
                $persennocallout =  ($data->total_nocallout_today == '0' && $data->total_call_today == '0') ? '0' : round(($data->total_nocallout_today / $data->total_call_today) * 100) . '%';

                $vToday = '<span style="color:#009b9b"><span title="total data hari ini">' . $vtdt . '</span>(<span title="sisah data kemarin">' . $vsisahkemarin . '</span>+<span title="data distribusi hari ini">' . $vdatatoday . '</span>)</span>';
                $vToday .= ' ( ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('0') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '&pageon=today" target="_blank"><span style="color:#eb7904" title="total telepon hari ini">' . $data->total_call_today . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0424" title="total belum telepon hari ini">' . $data->total_nocall . '</span>';
                $vToday .= ' )( ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('1') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '&pageon=today" target="_blank"><span style="color:#009b05" title="total diangkat hari ini">' . $data->total_callout_today . '(' . $persencallout . ')' . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a  href="/customer/callhistory?id=&param=' . encrypt('4') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '&pageon=today" target="_blank"><span style="color:#eb0424" title="total tidak diangkat hari ini">' . $data->total_nocallout_today . '(' . $persennocallout . ')' . '</span></a>';
                $vToday .= ' )( ';
                $vToday .= '<a href="#"><span style="color:#eb7904;font-weight: 600;" title="total prospek hari ini">' . $data->total_prospek_today . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a href="#"><span style="color:#009b05;font-weight: 600;" title="total closing hari ini">' . $data->total_closing_today . '</span></a>';
                $vToday .= ' ) ';
                return $vToday;
            })
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'campaign/group', 'type' => 'all']);
            })
            ->rawColumns(['today', 'all1', 'all2', 'all3', 'all4', 'action'])
            ->make(true);
    }
    public function dataTablesgrouplist(Request $request)
    {
        $groupid = decrypt($request->groupid);
        $data = Fileexcel::where('fileexcels.group_id', $groupid)
            ->without('Customer')
            ->without('User');
        //}
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'saveGrouplist(\'' . encrypt($data->id) . '\',\'delete\')', 'type' => 'onclick']);
            })
            ->make(true);
    }

    public function groupCampaignStore(Request $request, group_fileexcel $group_fileexcel)
    {

        $checkdata = ['id' => $group_fileexcel->id];
        $rules = [
            'nama'      => ['required'],
        ];
        if ($request->nama != $group_fileexcel->nama) {
            $rules = [
                'nama'      => ['required', 'unique:group_fileexcels'],
            ];
        }
        $validateData = $request->validate($rules);


        if (!isset($group_fileexcel->id)) {
            $validateData['created_at'] =  now();
            $validateData['created_id'] =  auth()->user()->id;
        }
        $validateData['updated_at'] =  now();

        group_fileexcel::updateOrInsert($checkdata, $validateData);
        if (isset($group_fileexcel->id)) {
            Session::flash('success', 'Data Berhasil diupdate!');
        } else {
            Session::flash('success', 'Data Berhasil ditambahkan!');
        }

        return redirect('/campaign/group');
    }

    public function getCampaignmodal()
    {
        $search = $_GET['id'];
        $result = [];
        $data = Fileexcel::select(
            'fileexcels.id',
            'fileexcels.kode',
            DB::raw('COUNT(IF(customers.status = "0", 1, NULL)) AS total_data'),
        )
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->whereNull('group_id')
            ->where('user_id', auth()->user()->id)
            ->where('kode', 'like', '%' . $search . '%')
            ->groupBy(DB::raw('1,2'))
            ->without('Customer');
        // $result = '<option value="">-- Pilih --</option>';
        $x = 0;
        foreach ($data->get() as $item) {
            $result[$x]['id'] = $item->id;
            $result[$x]['text'] = $item->kode;
            $x++;
        }
        return json_encode($result);
    }
    public function campaigngrouplistSave(Request $request)
    {
        $groupid = decrypt($request->group_id);
        if ($request->tipe == 'add') {
            $upData = ['group_id' => $groupid];
            $id = $request->fileexcel_id;
        } else if ($request->tipe == 'delete') {
            $upData = ['group_id' => null];
            $id = decrypt($request->fileexcel_id);
        }
        //$id = $request->fileexcel_id;

        //dd($upData);
        /** Update Call start time */
        Fileexcel::where('id', $id)
            ->Update($upData);
        if ($request->tipe == 'delete') {
            $result = 'Data Berhasil Dihapus!';
        } else {
            $result = 'Data Berhasil di ditambahkan!';
        }

        return json_encode($result);
        return redirect('/campaign/group');
    }
}
