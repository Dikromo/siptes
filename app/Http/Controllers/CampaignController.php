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
        $data = group_fileexcel::select(
            'group_fileexcels.id',
            'group_fileexcels.nama',
            DB::raw('GROUP_CONCAT(
                DISTINCT fileexcels.kode) grouplist'),
        )
            ->leftjoin('fileexcels', 'fileexcels.group_id', '=', 'group_fileexcels.id')
            ->join('users', 'users.id', '=', 'group_fileexcels.created_id');
        //if (auth()->user()->roleuser_id != '1') {
        $data = $data->where('group_fileexcels.created_id', auth()->user()->id)
            ->groupBy(DB::raw('1,2'));
        //}
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'campaign/group', 'type' => 'all']);
            })
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
