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
                    $hasil = date('Y-m-d', strtotime('-2 days', strtotime($param)));
                    break;
                default:
                    $hasil = $param;
                    break;
            }
        } else {
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
        $cektoday = date('Y-m-d');
        $cektoday2 = date('Y-m-d', strtotime($request->tanggal));
        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)), 'today');
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))), '');
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))), '');

        if ($request->tanggal == $cektoday) {
            $timerun1 = date('Y-m-d 00:00:00');
            $timerun2 = date('Y-m-d H:i:s');
            $jarak = date_diff(date_create($timerun2), date_create($timerun1));
            if ($jarak->h <= '10') {
                $runhour = '1';
            } else {
                if ($jarak->h >= '17') {
                    $runhour = '7';
                } else {
                    if ($jarak->h <= '12') {
                        if ($jarak->h == '12') {
                            $runhour = ((int) $jarak->h + 1 - 10) * 60;
                            $runhour = ($runhour) / 60;
                        } else {
                            $runhour = ((int) $jarak->h + 1 - 10) * 60;
                            $runhour = ($runhour + (int) $jarak->i) / 60;
                        }
                    } else {
                        $runhour = ((int) $jarak->h - 10) * 60;
                        $runhour = ($runhour + (int) $jarak->i) / 60;
                    }
                }
            }
        } else {
            $timerun1 = date('H:i:s', strtotime($request->tanggal . ' 00:00:00'));
            $timerun2 = date('H:i:s', strtotime($request->tanggal . ' 17:00:00'));
            $jarak = date_diff(date_create($timerun2), date_create($timerun1));
            $runhour = '7';
        }

        $data = User::select(
            'users.id',
            'users.name',
            DB::raw('IF(parentuser.name is not null, parentuser.name, users.name) as spvname'),
            DB::raw('IF(parentuser.nickname is not null, parentuser.nickname, users.nickname) as spvnickname'),
            'sm.name as smname',
            'sm.nickname as smnickname',
            'users.roleuser_id',
            DB::raw('COUNT(distribusis.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today . '" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_distoday'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_closing_today'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_prospek_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_dis2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_closing_2'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_prospek_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0"  AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_dis3'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '", 1, NULL)) AS total_nocall_3'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_callout_3'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_closing_3'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_prospek_3'),
        )
            ->leftjoin('distribusis', function ($join) use ($today, $today2, $today3) {
                $join->on('distribusis.user_id', '=', 'users.id');
                // ->where(function ($query)  use ($today, $today2, $today3) {
                //     $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                //         ->whereDate('distribusis.distribusi_at', '<=', $today);
                // });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->leftjoin('users as parentuser', 'parentuser.id', '=', 'users.parentuser_id')
            ->leftjoin('users as sm', 'sm.id', '=', 'users.sm_id')
            ->where('users.status', '1')
            ->whereDate('distribusis.distribusi_at', '<=', $today)
            ->where(function ($query) {
                $query->where('users.roleuser_id', '2')
                    ->orWhere('users.roleuser_id', '3');
            })
            ->where(function ($query) use ($cektoday) {
                $query->whereNull('users.flag_hadir')
                    ->orWhereRaw('date(users.flag_hadir) <> "' . $cektoday . '"');
            });
        if (auth()->user()->roleuser_id == '2') {
            $data = $data->where('users.parentuser_id', auth()->user()->id);
        } else if (auth()->user()->roleuser_id == '4') {
            $data = $data->where('users.cabang_id', auth()->user()->cabang_id);
        } else if (auth()->user()->roleuser_id == '5') {
            $data = $data->where('users.sm_id', auth()->user()->id);
        } else if (auth()->user()->roleuser_id == '6') {
            $data = $data->where('users.um_id', auth()->user()->id);
        }
        $data = $data->orderby('users.parentuser_id', 'asc')
            ->groupBy(DB::raw('1,2,3,4,5,6,7'));
        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('today', function ($data) use ($today) {
                $vtdt = $data->total_nocall + $data->total_call_today;
                $vToday = '<span style="color:#009b9b"><span title="total data hari ini">' . $vtdt . '</span>(<span title="sisah data kemarin">' . $vtdt - $data->total_call_distoday - $data->total_nocall_today  . '</span>+<span title="data distribusi hari ini">' . $vtdt - ($vtdt - $data->total_call_distoday - $data->total_nocall_today) . '</span>)</span>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=' . encrypt($data->id) . '&param=' . encrypt('0') . '&tanggal=' . encrypt($today) . '" target="_blank"><span style="color:#eb7904" title="total telepon hari ini">' . $data->total_call_today . '</span></a>';
                $vToday .= ' | ';
                if (auth()->user()->roleuser_id == '4') {
                    $vToday .= '<span style="color:#eb0424" title="total belum telepon hari ini">' . $data->total_nocall . '</span>';
                } else {
                    $vToday .= '<a href="/customer/distribusi?id=' . encrypt($data->id) . '" target="_blank"><span style="color:#eb0424" title="total belum telepon hari ini">' . $data->total_nocall . '</span></a>';
                }
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=' . encrypt($data->id) . '&param=' . encrypt('1') . '&tanggal=' . encrypt($today) . '" target="_blank"><span style="color:#009b05" title="total diangkat hari ini">' . $data->total_callout_today . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=' . encrypt($data->id) . '&param=' . encrypt('3') . '&tanggal=' . encrypt($today) . '" target="_blank"><span style="color:#eb7904;font-weight: 600;" title="total prospek">' . $data->total_prospek_today . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=' . encrypt($data->id) . '&param=' . encrypt('2') . '&tanggal=' . encrypt($today) . '" target="_blank"><span style="color:#009b05;font-weight: 600;" title="total closing">' . $data->total_closing_today . '</span></a>';

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
            ->addColumn('linkTotal', '/customer/callhistory?id=&param=' . encrypt('0') . '&tanggal=' . encrypt($today))
            ->addColumn('linkTotal1', '/customer/callhistory?id=&param=' . encrypt('1') . '&tanggal=' . encrypt($today))
            ->addColumn('linkTotalprospek1', '/customer/callhistory?id=&param=' . encrypt('3') . '&tanggal=' . encrypt($today))
            ->addColumn('linkTotalclosing1', '/customer/callhistory?id=&param=' . encrypt('2') . '&tanggal=' . encrypt($today))
            ->addColumn('signalCek', '{{$total_call_today}}')
            ->addColumn('totData', '{{($total_nocall + $total_call_today)}}')
            ->addColumn('totSisah', '{{($total_nocall + $total_call_today) - $total_call_distoday - $total_nocall_today}}')
            ->addColumn('totToday', '{{($total_nocall + $total_call_today)-(($total_nocall + $total_call_today) - $total_call_distoday - $total_nocall_today)}}')
            ->addColumn('h2', '{{$total_call_2.\' | \'.$total_callout_2.\' | \'.$total_prospek_2.\' | \'.$total_closing_2}}')
            ->addColumn('h3', '{{$total_call_3.\' | \'.$total_callout_3.\' | \'.$total_prospek_3.\' | \'.$total_closing_3}}')
            ->addColumn('total', '{{$total_nocall.\'\'}}')
            ->editColumn('total_data_today', '{{{$total_nocall + $total_call_today}}}')
            ->editColumn('name', function ($data) use ($cektoday2, $runhour) {
                $signalPercent = round((int)$data->total_call_today / (float)$runhour);
                $signalBar  = $data->roleuser_id == '2' ? '<i class="fas fa-star" style="color: #e7af13;"></i>' . $data->name : $data->name;
                $signalBar .=  $data->spvnickname == '' ? '(' . $data->spvname . ')' : '(' . $data->spvnickname . ')';
                $signalBar .= $data->smnickname == '' ? '(' . $data->smname . ')' : '(' . $data->smnickname . ')';
                if (date('l', strtotime($cektoday2)) != 'Sunday') {
                    if ($signalPercent >= '26') {
                        if ($signalPercent <= '34') {
                            $signalBar .= '<div class="progress vertical" style="height:10px;width:5px; margin-left:15px;">
                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                            </div>
                            </div>';
                        } else {
                            $signalBar .= '<div class="progress vertical" style="height:10px;width:5px; margin-left:15px;">
                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                            </div>
                            </div>';
                        }
                    }
                    if ($signalPercent >= '30') {
                        if ($signalPercent <= '34') {
                            $signalBar .= '<div class="progress vertical" style="height:15px;width:5px;margin-left:1px;">
                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                            </div>
                            </div>';
                        } else {
                            $signalBar .= '<div class="progress vertical" style="height:15px;width:5px;margin-left:1px;">
                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                            </div>
                            </div>';
                        }
                    }
                    if ($signalPercent >= '34') {
                        if ($signalPercent <= '34') {
                            $signalBar .= '<div class="progress vertical" style="height:20px;width:5px;margin-left:1px;">
                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                            </div>
                            </div>';
                        } else {
                            $signalBar .= '<div class="progress vertical" style="height:20px;width:5px;margin-left:1px;">
                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                            </div>
                            </div>';
                        }
                    }
                    if ($signalPercent >= '39') {
                        $signalBar .= '<div class="progress vertical" style="height:25px;width:5px;margin-left:1px;">
                <div class="progress-bar bg-success" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                </div>
                </div>';
                    }
                    if ($signalPercent >= '43') {
                        $signalBar .= '<div class="progress vertical" style="height:30px;width:5px;margin-left:1px;">
                <div class="progress-bar bg-success" role="progressbar" aria-valuenow="42" aria-valuemin="0" aria-valuemax="42" style="height: 100%">
                </div>
                </div>';
                    }
                }
                return $signalBar;
            })
            ->rawColumns(['today', 'name'])
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
            DB::raw('(COUNT(IF(distribusis.status = "0", 1, NULL)) + COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL))) AS sort_totaldata'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today . '" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_distoday'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_closing_today'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_prospek_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_dis2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_closing_2'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_prospek_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0"  AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_dis3'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '", 1, NULL)) AS total_nocall_3'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_callout_3'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_closing_3'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_prospek_3'),
        )
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin('distribusis', function ($join) use ($today, $today2, $today3, $request) {
                $join->on('distribusis.customer_id', '=', 'customers.id')
                    ->where(function ($query)  use ($today, $today2, $today3, $request) {
                        $query->where('distribusis.user_id',  $request->user_id);
                        // ->whereDate('distribusis.distribusi_at', '>=', $today3)
                        // ->whereDate('distribusis.distribusi_at', '<=', $today);
                    });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->join('users', 'users.id', '=', 'distribusis.user_id');

        $data = $data->orderby('sort_totaldata', 'desc')
            ->groupBy(DB::raw('1,2'))
            ->without('Customer');

        $i = 0;
        //echo $data->toSql();
        //exit;
        foreach ($data->get() as $item) {
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
            $cvtdt = $vtdt + $item->total_call_2 + $item->total_callout_2 + $item->total_call_3 + $item->total_callout_3;
            if ($cvtdt > '0') {
                $i++;
                $vToday = '<span style="color:#009b9b"><span title="total data hari ini">' . $vtdt . '</span>(<span title="sisah data kemarin">' . $vtdt - $item->total_call_distoday - $item->total_nocall_today  . '</span>+<span title="data distribusi hari ini">' . $vtdt - ($vtdt - $item->total_call_distoday - $item->total_nocall_today) . '</span>)</span>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb7904" title="total telepon hari ini">' . $item->total_call_today . '</span>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0423" title="total belum telepon hari ini">' . $item->total_nocall . '</span>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#009b05" title="total diangkat hari ini">' . $item->total_callout_today . '</span>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb7904;font-weight: 400;" title="total prospek">' . $item->total_prospek_today . '</span>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#009b05;font-weight: 400;" title="total closing">' . $item->total_closing_today . '</span>';
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
            <td>' . $item->total_call_2 . ' | ' . $item->total_callout_2 . ' | ' . $item->total_prospek_2 . ' | ' . $item->total_closing_2 . '</td>
            <td>' . $item->total_call_3 . ' | ' . $item->total_callout_3 . ' | ' . $item->total_prospek_3 . ' | ' . $item->total_closing_3 . '</td>
        </tr>';
            }
        }
        $hasil .= '</tbody></table>';
        return json_encode($hasil);
    }
    public function campaigncall()
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
            'admin.pages.dashboard.dashboardcampaign',
            [
                'title' => 'Dashboard Campaign',
                'active' => 'dashboardcampaign',
                'active_sub' => '',
                "userData" => $userSelect->get(),
                'data' => ''
            ]
        );
    }
    public function getCampaigncall(Request $request)
    {
        $today = $this->checkDay(date('Y-m-d', strtotime($request->tanggal)), 'today');
        $today2 = $this->checkDay(date('Y-m-d', strtotime('-1 days', strtotime($today))), '');
        $today3 = $this->checkDay(date('Y-m-d', strtotime('-2 days', strtotime($today))), '');

        $lastDistribusi = DB::table('distribusis')
            ->select('customer_id', DB::raw('MAX(distribusis.id) as id'))
            ->join('users', 'users.id', '=', 'distribusis.user_id');
        if (auth()->user()->roleuser_id != '1') {
            $lastDistribusi = $lastDistribusi->whereRaw('users.cabang_id = "' . auth()->user()->cabang_id . '"');
        }
        $lastDistribusi = $lastDistribusi->groupBy('customer_id');

        $data = Fileexcel::select(
            'fileexcels.id',
            'fileexcels.kode',
            DB::raw('(COUNT(IF(distribusis.status = "0", 1, NULL)) + COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL))) AS sort_totaldata'),
            DB::raw('COUNT(customers.id) AS total_data'),
            DB::raw('COUNT(IF(distribusis.status is null, 1, NULL)) AS total_nodistribusi'),
            DB::raw('COUNT(distribusis.id) AS total_data1'),
            DB::raw('COUNT(IF(distribusis.status <> "0", 1, NULL)) AS total_call'),
            DB::raw('COUNT(IF(distribusis.status = "0", 1, NULL)) AS total_nocall'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1", 1, NULL)) AS total_callout'),
            DB::raw('COUNT(IF(statuscalls.jenis = "2", 1, NULL)) AS total_nocallout'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15"), 1, NULL)) AS total_closing'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34"), 1, NULL)) AS total_prospek'),
            DB::raw('COUNT(IF(DATE(distribusis.updated_at) = "' . $today . '",1, NULL)) AS total_data_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_today'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today . '" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_call_distoday'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today . '", 1, NULL)) AS total_nocall_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_callout_today'),
            DB::raw('COUNT(IF(statuscalls.jenis = "2" AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_nocallout_today'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_closing_today'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today . '", 1, NULL)) AS total_prospek_today'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today2 . '",1, NULL)) AS total_data_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_2'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_call_dis2'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today2 . '", 1, NULL)) AS total_nocall_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_callout_2'),
            DB::raw('COUNT(IF(statuscalls.jenis = "2" AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_nocallout_2'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_closing_2'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today2 . '", 1, NULL)) AS total_prospek_2'),
            DB::raw('COUNT(IF(DATE(distribusis.distribusi_at) = "' . $today3 . '",1, NULL)) AS total_data_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0"  AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_3'),
            DB::raw('COUNT(IF(distribusis.status <> "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_call_dis3'),
            DB::raw('COUNT(IF(distribusis.status = "0" AND DATE(distribusis.distribusi_at) = "' . $today3 . '", 1, NULL)) AS total_nocall_3'),
            DB::raw('COUNT(IF(statuscalls.jenis = "1" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_callout_3'),
            DB::raw('COUNT(IF(statuscalls.jenis = "2" AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_nocallout_3'),
            DB::raw('COUNT(IF((distribusis.status = "1" OR distribusis.status = "15") AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_closing_3'),
            DB::raw('COUNT(IF((distribusis.status = "2" OR distribusis.status = "34") AND DATE(distribusis.updated_at) = "' . $today3 . '", 1, NULL)) AS total_prospek_3'),
        )
            ->join('customers', 'customers.fileexcel_id', '=', 'fileexcels.id')
            ->leftjoin(DB::raw('(' . $lastDistribusi->toSql() . ') as a'), function ($join) {
                $join->on('customers.id', '=', 'a.customer_id');
            })
            ->leftjoin('distribusis', function ($join) use ($today, $today2, $today3, $request) {
                $join->on('distribusis.id', '=', 'a.id');
                // ->where(function ($query)  use ($today, $today2, $today3) {
                //     $query->whereDate('distribusis.distribusi_at', '>=', $today3)
                //         ->whereDate('distribusis.distribusi_at', '<=', $today);
                // });
            })
            ->leftjoin('statuscalls', 'statuscalls.id', '=', 'distribusis.status')
            ->leftjoin('users', 'users.id', '=', 'distribusis.user_id');
        //     ->where('users.roleuser_id', '3');
        // if (auth()->user()->roleuser_id != '1') {
        //     $data = $data->where('users.parentuser_id', auth()->user()->id);
        // }
        if (auth()->user()->roleuser_id != '1') {
            $data = $data->whereIn('fileexcels.user_id', [auth()->user()->id, '31']);
        }
        $data = $data->orderby('sort_totaldata', 'desc')
            ->groupBy(DB::raw('1,2'))
            ->without("Customer");

        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('all', function ($data) use ($today) {
                $persendis =  ($data->total_data == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_data1 / $data->total_data) * 100) . '%';
                $persennodis =  ($data->total_data == '0' && $data->total_nodistribusi == '0') ? '0' : round(($data->total_nodistribusi / $data->total_data) * 100) . '%';
                $persencall =  ($data->total_call == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_call / $data->total_data1) * 100) . '%';
                $persennocall =  ($data->total_nocall == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_nocall / $data->total_data1) * 100) . '%';
                $persencallout =  ($data->total_callout == '0' && $data->total_call == '0') ? '0' : round(($data->total_callout / $data->total_call) * 100) . '%';
                $persennocallout =  ($data->total_nocallout == '0' && $data->total_call == '0') ? '0' : round(($data->total_nocallout / $data->total_call) * 100) . '%';
                $persenprospek =  ($data->total_prospek == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_prospek / $data->total_data1) * 100) . '%';
                $persenclosing =  ($data->total_closing == '0' && $data->total_data1 == '0') ? '0' : round(($data->total_closing / $data->total_data1) * 100) . '%';
                $vToday = '<span style="color:#009b9b"><span title="total data">' . $data->total_data . '</span>';
                $vToday .= ' ( ';
                $vToday .= '<span style="color:#eb7904" title="total data terdistribusi">' . $data->total_data1 . '(' . $persendis . ')' . '</span>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0424" title="total belum terdistribusi">' . $data->total_nodistribusi . '(' . $persennodis . ')' . '</span>';
                $vToday .= ' )( ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('0') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#eb7904" title="total telepon">' . $data->total_call . '(' . $persencall . ')' . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0424" title="total belum telepon">' . $data->total_nocall . '(' . $persennocall . ')' . '</span>';
                $vToday .= ' )( ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('1') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#009b05" title="total contact">' . $data->total_callout . '(' . $persencallout . ')' . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('4') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#eb0424" title="total not contact">' . $data->total_nocallout . '(' . $persennocallout . ')' . '</span></a>';
                $vToday .= ' ) ';
                // $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('3') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#eb7904;font-weight: 600;" title="total prospek">' . $data->total_prospek . '(' . $persenprospek . ')' . '</span></a>';
                // $vToday .= ' | ';
                // $vToday .= '<a href="/customer/callhistory?id=&param=' . encrypt('2') . '&tanggal=' . encrypt($today) . '&idcampaign=' . encrypt($data->id) . '" target="_blank"><span style="color:#009b05;font-weight: 600;" title="total closing">' . $data->total_closing . '(' . $persenclosing . ')' . '</span></a>';
                return $vToday;
            })
            ->addColumn('today', function ($data) use ($today) {
                $vtdt = $data->total_nocall + $data->total_call_today;
                $vsisahkemarin = $vtdt - $data->total_call_distoday - $data->total_nocall_today;
                $vdatatoday = $vtdt - ($vtdt - $data->total_call_distoday - $data->total_nocall_today);
                $persennodis =  ($vtdt == '0' && $vsisahkemarin == '0') ? '0' : round(($vsisahkemarin / $vtdt) * 100) . '%';
                $persendis =  ($vtdt == '0' && $vdatatoday == '0') ? '0' : round(($vdatatoday / $vtdt) * 100) . '%';
                $persencall =  ($data->total_call_today == '0' && $vtdt == '0') ? '0' : round(($data->total_call_today / $vtdt) * 100) . '%';
                $persennocall =  ($data->total_nocall == '0' && $vtdt == '0') ? '0' : round(($data->total_nocall / $vtdt) * 100) . '%';
                $persencallout =  ($data->total_callout_today == '0' && $data->total_call_today == '0') ? '0' : round(($data->total_callout_today / $data->total_call_today) * 100) . '%';
                $persennocallout =  ($data->total_nocallout_today == '0' && $data->total_call_today == '0') ? '0' : round(($data->total_nocallout_today / $data->total_call_today) * 100) . '%';

                $vToday = '<span style="color:#009b9b"><span title="total data hari ini">' . $vtdt . '</span>(<span title="sisah data kemarin">' . $vsisahkemarin . '(' . $persennodis . ')' . '</span>+<span title="data distribusi hari ini">' . $vdatatoday . '(' . $persendis . ')' . '</span>)</span>';
                $vToday .= ' ( ';
                $vToday .= '<a href="#"><span style="color:#eb7904" title="total telepon hari ini">' . $data->total_call_today . '(' . $persencall . ')' . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<span style="color:#eb0424" title="total belum telepon hari ini">' . $data->total_nocall . '(' . $persennocall . ')' . '</span>';
                $vToday .= ' )( ';
                $vToday .= '<a href="#"><span style="color:#009b05" title="total diangkat hari ini">' . $data->total_callout_today . '(' . $persencallout . ')' . '</span></a>';
                $vToday .= ' | ';
                $vToday .= '<a href="#"><span style="color:#eb0424" title="total diangkat hari ini">' . $data->total_nocallout_today . '(' . $persennocallout . ')' . '</span></a>';
                $vToday .= ' ) ';
                // $vToday .= '<a href="#"><span style="color:#eb7904;font-weight: 600;" title="total prospek hari ini">' . $data->total_prospek_today . '</span></a>';
                // $vToday .= ' | ';
                // $vToday .= '<a href="#"><span style="color:#009b05;font-weight: 600;" title="total closing hari ini">' . $data->total_closing_today . '</span></a>';
                return $vToday;
            })
            ->addColumn('totData', '{{($total_nocall + $total_call_today)}}')
            ->addColumn('totSisah', '{{($total_nocall + $total_call_today) - $total_call_distoday - $total_nocall_today}}')
            ->addColumn('totToday', '{{($total_nocall + $total_call_today)-(($total_nocall + $total_call_today) - $total_call_distoday - $total_nocall_today)}}')
            ->addColumn('h2', '{{$total_call_2.\' | \'.$total_callout_2.\' | \'.$total_nocallout_2}}')
            ->addColumn('h3', '{{$total_call_3.\' | \'.$total_callout_3.\' | \'.$total_nocallout_3}}')
            ->addColumn('total', '{{$total_nocall.\'\'}}')
            ->editColumn('total_data_today', '{{{$total_nocall + $total_call_today}}}')
            ->rawColumns(['today', 'all'])
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
