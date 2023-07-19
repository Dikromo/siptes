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

        $statuscall = ['Total Data', 'Belum Ditelfon'];
        $dataStatuscall = Statuscall::latest();
        foreach ($dataStatuscall->get() as $item) {
            array_push($statuscall, $item->nama);
        }

        return view(
            'admin.pages.dashboard.dashboardsales',
            [
                'title' => 'Dashboard Sales',
                'active' => 'dashboardsales',
                'active_sub' => '',
                "statusCall" => $statuscall,
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
        $result['hariini'] = (isset($request->tanggal)) ? date('d F Y', strtotime($request->tanggal)) : date('d F Y');
        $hariini = (isset($request->tanggal)) ? date('Y-m-d', strtotime($request->tanggal)) : date('Y-m-d');

        $dataUser = User::select('name')
            ->where('id', $request->user_id);
        foreach ($dataUser->get() as $items) {
            $result['nama'] = $items->name;
        }

        // Total Data
        $dataAll = Distribusi::select(
            DB::raw("COUNT(id) as count")
        )
            ->where('user_id', $request->user_id)
            ->whereDate('distribusi_at', $hariini);
        foreach ($dataAll->get() as $items) {
            array_push($result['salescall'], $items->count);
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
            ->whereDate('distribusi_at', $hariini);
        foreach ($dataAll->get() as $items) {
            array_push($result['salescall'], $items->count);
        }

        // Get Data By Status Call
        foreach ($dataStatuscall->get() as $item) {
            # code...
            $data = Distribusi::select(
                DB::raw("COUNT(id) as count")
            )
                ->where('user_id', $request->user_id)
                ->where('status', $item->id)
                ->whereDate('distribusi_at', $hariini);
            foreach ($data->get() as $items) {
                array_push($result['salescall'], $items->count);
            }
        }


        return json_encode($result);
    }
}
