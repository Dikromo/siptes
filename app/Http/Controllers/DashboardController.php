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
        } else if (auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '5') {
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
        if (auth()->user()->cabang_id == '4' || auth()->user()->roleuser_id == '1') {
        } else {
            $dataStatuscall = $dataStatuscall->where('cabang_id', '0');
        }
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
    private function checkDay($param, $param2)
    {
        $hasil = '';
        if ($param2 == 'today') {
            switch (date('l', strtotime($param))) {
                case 'Sunday':
                    $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                    break;
                default:
                    $hasil = $param;
                    break;
            }
        } else {
            switch (date('l', strtotime($param))) {
                case 'Saturday':
                    $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                    break;

                case 'Sunday':
                    $hasil = date('Y-m-d', strtotime('-1 days', strtotime($param)));
                    break;
                default:
                    $hasil = $param;
                    break;
            }
        }
        return $hasil;
    }
    public function getSalescall2_header(Request $request)
    {
        $result = [];
        $result['today'] = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)), 'today');
        $result['h1'] = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($result['today']))), '');
        $result['h2'] = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($result['today']))), '');

        return $result;
    }
    public function getSalescall2(Request $request)
    {
        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)), 'today');
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))), '');
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))), '');
        $data = User::select(
            'users.id',
            'users.name',
            'parentuser.name as spvname',
            DB::raw('COUNT(distribusis.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today . '" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_distoday'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_dis2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0"  AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_dis3'),
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
            ->leftjoin('users as parentuser', 'parentuser.id', '=', 'users.parentuser_id')
            ->where('users.roleuser_id', '3');
        if (auth()->user()->roleuser_id == '2') {
            $data = $data->where('users.parentuser_id', auth()->user()->id);
        } else if (auth()->user()->roleuser_id == '4') {
            $data = $data->where('users.cabang_id', auth()->user()->cabang_id);
        }
        $data = $data->orderby('users.parentuser_id', 'asc')
            ->groupBy(DB::raw('1,2,3'));
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('today', function ($data) use ($today) {
                $vtdt = $data->total_nocall + $data->total_call_today;
                $vToday = '<span style="color:#009b9b"><span title="total data hari ini">' . $vtdt . '</span>(<span title="sisah data kemarin">' . $vtdt - $data->total_call_distoday - $data->total_nocall_today  . '</span>+<span title="data distribusi hari ini">' . $vtdt - ($vtdt - $data->total_call_distoday - $data->total_nocall_today) . '</span>)</span>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=' . encrypt($data->id) . '&param=' . encrypt('0') . '&tanggal=' . encrypt($today) . '"><span style="color:#eb7904" title="total telepon hari ini">' . $data->total_call_today . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0423" title="total belum telepon hari ini">' . $data->total_nocall . '</span>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=' . encrypt($data->id) . '&param=' . encrypt('1') . '&tanggal=' . encrypt($today) . '"><span style="color:#009b05" title="total diangkat hari ini">' . $data->total_callout_today . '</span></a>';

                // $vtdt = $data->total_nocall + $data->total_call_today;
                // $vToday = '<span style="color:#009b9b"><span title="total data hari ini">' . $vtdt . '</span>(<span title="sisah data kemarin">' . $vtdt - $data->total_call_distoday - $data->total_nocall_today  . '</span>+<span title="data distribusi hari ini">' . $vtdt - ($vtdt - $data->total_call_distoday - $data->total_nocall_today) . '</span>)</span>';
                // $vToday .= ' | ';
                // $vToday .= '<a href="/customer/callhistory?id=' . encrypt($data->id) . '&param=' . encrypt('0') . '&tanggal=' . encrypt($today) . '"><span style="color:#eb7904" title="total telepon hari ini">' . $data->total_call_today . '</span></a>';
                // $vToday .= ' | ';
                // $vToday .= '<span style="color:#eb0423" title="total belum telepon hari ini">' . $data->total_nocall . '</span>';
                // $vToday .= ' | ';
                // $vToday .= '<span style="color:#009b05" title="total diangkat hari ini">' . $data->total_callout_today . '</span>';

                //$vToday = '<span style="color:#a30">' . $vtdt . '</span>';
                // $vToday = '{{\'<span style="color:#a30">\'.$data->total_nocall + $data->total_call_today.\'(\'.$total_nocall-$total_nocall_today.\' + \'.$total_nocall_today.\')</span>
                //     | \'.$total_call_today.\' | \'.$total_nocall.\' | \'.$total_callout_today}}';
                return $vToday;
            })
            ->addColumn('h2', '{{$total_call_2.\' | \'.$total_callout_2}}')
            ->addColumn('h3', '{{$total_call_3.\' | \'.$total_callout_3}}')
            ->addColumn('total', '{{$total_nocall.\'\'}}')
            ->editColumn('total_data_today', '{{{$total_nocall + $total_call_today}}}')
            ->editColumn('name', '{{{$name}}} ({{{$spvname}}})')
            ->rawColumns(['today'])
            ->make(true);
    }
    public function getSalescall2_detail(Request $request)
    {

        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)), 'today');
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))), '');
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))), '');

        $hasil = '<table class="table table-head-fixed text-nowrap text-center">
        <thead>
            <tr>
                <th></th>
                <th>NO</th>
                <th>Kode</th>
                <th>' . date('l, d-M-Y', strtotime($today)) . '</th>
                <th>' . date('l, d-M-Y', strtotime($today2)) . '</th>
                <th>' . date('l, d-M-Y', strtotime($today3)) . '</th>
                <!--th>Tanggal Call Out</th>
                <th>H-1 Data</th>
                <th>H-1 Sudah Di Telepon</th>
                <th>H-1 Belum Di Telepon</th>
                <th>H-1 Call Out</th>
                <th>H-2 Data</th>
                <th>H-2 Sudah Di Telepon</th>
                <th>H-2 Belum Di Telepon</th>
                <th>H-2 Call Out</th-->
            </tr>
        </thead>
        <tbody>';

        $data = Fileexcel::select(
            'fileexcels.id',
            'fileexcels.kode',
            DB::raw('COUNT(distribusis.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today . '" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_distoday'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_dis2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0"  AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_dis3'),
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

        $data = $data->orderby('fileexcels.id', 'desc')
            ->groupBy(DB::raw('1,2'));

        $i = 0;
        foreach ($data->get() as $item) {
            $i++;
            //     $hasil .= '<tr>
            //     <td></td>
            //     <td>' . $i . '</td>
            //     <td>' . $item->kode . '</td>
            //     <td>' . $item->total_call_today + $item->total_nocall . '</td>
            //     <td>' . $item->total_call_today . '</td>
            //     <td>' . $item->total_nocall . '</td>
            //     <td>' . $item->total_callout_today . '</td>
            //     <td>' . $item->total_data_2 . '</td>
            //     <td>' . $item->total_call_2 . '</td>
            //     <td>' . $item->total_nocall_2 . '</td>
            //     <td>' . $item->total_callout_2 . '</td>
            //     <td>' . $item->total_data_3 . '</td>
            //     <td>' . $item->total_call_3 . '</td>
            //     <td>' . $item->total_nocall_3 . '</td>
            //     <td>' . $item->total_callout_3 . '</td>
            // </tr>';
            $vtdt = $item->total_nocall + $item->total_call_today;
            $vToday = '<span style="color:#009b9b"><span title="total data hari ini">' . $vtdt . '</span>(<span title="sisah data kemarin">' . $vtdt - $item->total_call_distoday - $item->total_nocall_today  . '</span>+<span title="data distribusi hari ini">' . $vtdt - ($vtdt - $item->total_call_distoday - $item->total_nocall_today) . '</span>)</span>';
            $vToday .= ' | ';
            $vToday .= '<span style="color:#eb7904" title="total telepon hari ini">' . $item->total_call_today . '</span>';
            $vToday .= ' | ';
            $vToday .= '<span style="color:#eb0423" title="total belum telepon hari ini">' . $item->total_nocall . '</span>';
            $vToday .= ' | ';
            $vToday .= '<span style="color:#009b05" title="total diangkat hari ini">' . $item->total_callout_today . '</span>';
            // $vToday = '<span style="color:#009b9b">' . $vtdt . '(' . $vtdt - $item->total_call_distoday - $item->total_nocall_today  . ' + ' . $vtdt - ($vtdt - $item->total_call_distoday - $item->total_nocall_today) . ')</span>';
            // $vToday .= ' | ';
            // $vToday .= '<span style="color:#eb7904">' . $item->total_call_today . '</span>';
            // $vToday .= ' | ';
            // $vToday .= '<span style="color:#eb0423">' . $item->total_nocall . '</span>';
            // $vToday .= ' | ';
            // $vToday .= '<span style="color:#009b05">' . $item->total_callout_today . '</span>';
            $hasil .= '<tr>
            <td></td>
            <td>' . $i . '</td>
            <td>' . $item->kode . '</td>
            <td>' . $vToday . '</td>
            <td>' . $item->total_call_2 . ' | ' . $item->total_callout_2 . '</td>
            <td>' . $item->total_call_3 . ' | ' . $item->total_callout_3 . '</td>
        </tr>';
        }
        $hasil .= '</tbody></table>';
        return json_encode($hasil);
    }
    public function getCampaigncall(Request $request)
    {
        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)), 'today');
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))), '');
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))), '');
        $data = Fileexcel::select(
            'fileexcels.id',
            'fileexcels.kode',
            DB::raw('COUNT(distribusis.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today . '" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_distoday'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_dis2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0"  AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_dis3'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '", 1, NULL)) AS total_nocall_3'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_callout_3'),
        )
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin('distribusis', function ($join) use ($today, $today2, $today3, $request) {
                $join->on('distribusis.customer_id', '=', 'customers.id')
                    ->where(function ($query)  use ($today, $today2, $today3) {
                        $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                            ->whereDate('distribusis.distribusi_at', '<=', $today);
                    });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->join('users', 'users.id', '=', 'distribusis.user_id')
            ->where('users.roleuser_id', '3');
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->where('users.parentuser_id', auth()->user()->id);
        }
        $data = $data->orderby('fileexcels.id', 'desc')
            ->groupBy(DB::raw('1,2'))
            ->without("Customer");

        return DataTables::of($data->get())
            ->addIndexColumn()
            ->editColumn('total_data_today', '{{{$total_nocall + $total_call_today}}}')
            ->make(true);
    }
    public function getCampaigncall_detail(Request $request)
    {
        $hasil = '<table class="table table-head-fixed text-nowrap text-center">
        <thead>
            <tr>
                <th></th>
                <th>NO</th>
                <th>Nama</th>
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
        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)), 'today');
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))), '');
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))), '');
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

            ->join('distribusis', function ($join) use ($today, $today2, $today3, $request) {
                $join->on('distribusis.user_id', '=', 'users.id')
                    ->where(function ($query)  use ($today, $today2, $today3, $request) {
                        $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                            ->whereDate('distribusis.distribusi_at', '<=', $today);
                    });
            })

            ->join('customers', function ($join) use ($today, $today2, $today3, $request) {
                $join->on('customers.id', '=', 'distribusis.customer_id')
                    ->where(function ($query)  use ($today, $today2, $today3, $request) {
                        $query->where('customers.fileexcel_id', '=', $request->user_id);
                    });
            })->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->where('users.roleuser_id', '3');
        $data = $data->orderby('users.parentuser_id', 'asc')
            ->groupBy(DB::raw('1,2'));

        $i = 0;
        foreach ($data->get() as $item) {
            $i++;
            $hasil .= '<tr>
            <td></td>
            <td>' . $i . '</td>
            <td>' . $item->name . '</td>
            <td>' . $item->total_call_today + $item->total_nocall . '</td>
            <td>' . $item->total_call_today . '</td>
            <td>' . $item->total_nocall . '</td>
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
