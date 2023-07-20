<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Distribusi;
use App\Models\Statuscall;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function salescall()
    {
        $userSelect = User::where('status', '1');
        if (auth()->user()->roleuser_id == '2') {
            $userSelect = $userSelect->where('parentuser_id', auth()->user()->id)
                ->where('roleuser_id', '3');
        } else {
            $userSelect = $userSelect->where(function ($query) {
                $query->where('roleuser_id', '2')
                    ->orWhere('roleuser_id', '3');
            });
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
        $dataStatuscall = Statuscall::latest();
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
}
