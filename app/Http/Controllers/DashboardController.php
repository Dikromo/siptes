<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Fileexcel;
use App\Models\Distribusi;
use App\Models\Statuscall;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    //
    public function salescall()
    {
        $userSelect = User::where('status', '1');
        if (auth()->user()->roleuser_id == '2') {
            $userSelect = $userSelect->where('parentuser_id', auth()->user()->id)
                ->where('roleuser_id', '3');
        } else if (auth()->user()->roleuser_id == '5') {
            $userSelect = $userSelect->where('cabang_id', auth()->user()->cabang_id)
                ->Where('roleuser_id', '3');
        } else {
            $userSelect = $userSelect->where('roleuser_id', '3');
        }

        return view(
            'admin.pages.dashboard.dashboardsales',
            [
                'title' => 'Dashboard Sales',
                'active' => 'dashboardsales',
                'active_sub' => '',
                "userData" => $userSelect->get(),
                'data' => ''
            ]
        );
    }
    public function getSalescall(Request $request)
    {
        $dataStatuscall = Statuscall::orderby('jenis', 'asc')
            ->orderby('id', 'asc');
        $result = [];
        $result['salescall'] = [];
        $result['statuscall'] = (isset($request->date_by) && $request->date_by == 'updated_at') ? [] : ['Total Data', 'Belum Ditelfon'];
        $result['hariini'] = (isset($request->tanggal)) ? date('d F Y', strtotime($request->tanggal)) : date('d F Y');
        $hariini = (isset($request->tanggal)) ? date('Y-m-d', strtotime($request->tanggal)) : date('Y-m-d');
        $date_by = (isset($request->date_by)) ? $request->date_by : 'distribusi_at';

        $dataUser = User::select('name')
            ->where('id', $request->user_id);
        foreach ($dataUser->get() as $items) {
            $result['nama'] = $items->name;
        }

        // Get Status
        foreach ($dataStatuscall->get() as $item) {
            array_push($result['statuscall'], $item->nama);
        }
        if (isset($request->date_by) && $request->date_by != 'updated_at') {
            // Total Data
            $dataAll = Distribusi::select(
                DB::raw("COUNT(id) as count")
            )
                ->where('user_id', $request->user_id)
                ->whereDate($date_by, $hariini);
            foreach ($dataAll->get() as $items) {
                array_push($result['salescall'], (int)$items->count);
            }

            // Get Data Status 0
            $dataAll = Distribusi::select(
                DB::raw("COUNT(id) as count")
            )
                ->where('user_id', $request->user_id)
                ->where(function ($query) {
                    $query->where('status', '0')
                        ->orWhere('status', null);
                })
                ->whereDate($date_by, $hariini);
            foreach ($dataAll->get() as $items) {
                array_push($result['salescall'], (int)$items->count);
            }
        }
        // Get Data By Status Call
        foreach ($dataStatuscall->get() as $item) {
            # code...
            $data = Distribusi::select(
                DB::raw("COUNT(id) as count")
            )
                ->where('user_id', $request->user_id)
                ->where('status', $item->id)
                ->whereDate($date_by, $hariini);
            foreach ($data->get() as $items) {
                array_push($result['salescall'], (int)$items->count);
            }
        }


        return json_encode($result);
    }
    public function salescall2()
    {
        $userSelect = User::where('status', '1');
        if (auth()->user()->roleuser_id == '2') {
            $userSelect = $userSelect->where('parentuser_id', auth()->user()->id)
                ->where('roleuser_id', '3');
        } else if (auth()->user()->roleuser_id == '5') {
            $userSelect = $userSelect->where('cabang_id', auth()->user()->cabang_id)
                ->Where('roleuser_id', '3');
        } else {
            $userSelect = $userSelect->where('roleuser_id', '3');
        }

        return view(
            'admin.pages.dashboard.dashboardsales2',
            [
                'title' => 'Dashboard Sales',
                'active' => 'dashboardsales2',
                'active_sub' => '',
                "userData" => $userSelect->get(),
                'data' => ''
            ]
        );
    }
    private function checkDay($param)
    {
        $hasil = '';
        switch (date('l', strtotime($param))) {
            case 'Saturday':
                $hasil = date('Y-m-d', strtotime('-2 days', strtotime($param)));
                break;

            case 'Sunday':
                $hasil = date('Y-m-d', strtotime('-2 days', strtotime($param)));
                break;
            default:
                $hasil = $param;
                break;
        }
        return $hasil;
    }
    public function getSalescall2(Request $request)
    {
        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)));
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))));
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))));
        $data = User::select(
            'users.id',
            'users.name',
            DB::raw('COUNT(distribusis.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '", 1, NULL)) AS total_nocall_3'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_callout_3'),
        )
            ->join('distribusis', function ($join) use ($today, $today2, $today3) {
                $join->on('distribusis.user_id', '=', 'users.id')
                    ->where(function ($query)  use ($today, $today2, $today3) {
                        $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                            ->whereDate('distribusis.distribusi_at', '<=', $today);
                    });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->where('users.roleuser_id', '3');
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->where('users.parentuser_id', auth()->user()->id);
        }
        $data = $data->orderby('users.parentuser_id', 'asc')
            ->groupBy(DB::raw('1,2'));
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->editColumn('total_data_today', '{{{$total_nocall + $total_call_today}}}')
            ->make(true);
    }
    public function getSalescall2_detail(Request $request)
    {
        $hasil = '<table class="table table-head-fixed text-nowrap text-center">
        <thead>
            <tr>
                <th></th>
                <th>NO</th>
                <th>Kode</th>
                <th>Tanggal Data</th>
                <th>Tanggal Sudah Di Telepon</th>
                <th>Tanggal Belum Di Telepon</th>
                <th>Tanggal Call Out</th>
                <th>H-1 Data</th>
                <th>H-1 Sudah Di Telepon</th>
                <th>H-1 Belum Di Telepon</th>
                <th>H-1 Call Out</th>
                <th>H-2 Data</th>
                <th>H-2 Sudah Di Telepon</th>
                <th>H-2 Belum Di Telepon</th>
                <th>H-2 Call Out</th>
            </tr>
        </thead>
        <tbody>';
        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)));
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))));
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))));
        $data = Fileexcel::select(
            'fileexcels.id',
            'fileexcels.kode',
            DB::raw('COUNT(distribusis.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(distribusis.status = "0",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '", 1, NULL)) AS total_nocall_3'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_callout_3'),
        )
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin('distribusis', function ($join) use ($today, $today2, $today3, $request) {
                $join->on('distribusis.customer_id', '=', 'customers.id')
                    ->where(function ($query)  use ($today, $today2, $today3, $request) {
                        $query->where('distribusis.user_id',  $request->user_id)
                            ->whereDate('distribusis.distribusi_at', '>=', $today3)
                            ->whereDate('distribusis.distribusi_at', '<=', $today);
                    });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->join('users', 'users.id', '=', 'distribusis.user_id')
            ->where('users.roleuser_id', '3');

        $data = $data->orderby('users.parentuser_id', 'asc')
            ->groupBy(DB::raw('1,2'));

        $i = 0;
        foreach ($data->get() as $item) {
            $i++;
            $hasil .= '<tr>
            <td></td>
            <td>' . $i . '</td>
            <td>' . $item->kode . '</td>
            <td>' . $item->total_call_today + $item->total_nocall_today . '</td>
            <td>' . $item->total_call_today . '</td>
            <td>' . $item->total_nocall_today . '</td>
            <td>' . $item->total_callout_today . '</td>
            <td>' . $item->total_data_2 . '</td>
            <td>' . $item->total_call_2 . '</td>
            <td>' . $item->total_nocall_2 . '</td>
            <td>' . $item->total_callout_2 . '</td>
            <td>' . $item->total_data_3 . '</td>
            <td>' . $item->total_call_3 . '</td>
            <td>' . $item->total_nocall_3 . '</td>
            <td>' . $item->total_callout_3 . '</td>
        </tr>';
        }
        $hasil .= '</tbody></table>';
        return json_encode($hasil);
    }
}
