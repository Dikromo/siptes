<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Fileexcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
}
