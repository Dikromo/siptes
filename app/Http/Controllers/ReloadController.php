<?php

namespace App\Http\Controllers;

use App\Models\Fileexcel;
use App\Models\User;
use App\Models\Produk;
use App\Models\Statuscall;
use Illuminate\Http\Request;

class ReloadController extends Controller
{
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
        if (auth()->user()->roleuser_id != '1') {
            if (auth()->user()->cabang_id == '4') {
                $statusSelect =    $statusSelect->whereIn('id', ['15']);
                //$statusSelect =    $statusSelect->whereIn('id', ['15', '16', '34']);
            } else {
                $statusSelect =    $statusSelect->whereIn('id', ['1']);
                //$statusSelect =    $statusSelect->whereIn('id', ['1', '2', '3']);
            }
        } else {
            $statusSelect =    $statusSelect->whereIn('id', ['1', '15']);
        }
        $fileSelect = Fileexcel::where('user_id', '31')
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
}
