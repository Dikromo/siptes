<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CallpagesController extends Controller
{
    //
    public function salesCallpages()
    {
        return view('sales.pages.call.index', [
            'title' => 'Call Page',
            'active' => 'call',
            'active_sub' => 'call',
            "data" => Distribusi::where('user_id', auth()->user()->id)
                ->where('status', 0)
                ->latest()
                ->paginate(1),
            //"category" => User::all(),
        ]);
    }
    public function salesCallshow($id)
    {
        $upData = ['call_time' => now()];
        $id = decrypt($id);

        //dd($upData);
        /** Update Call start time */
        Distribusi::where('id', $id)
            ->Update($upData);


        $distribusi = Distribusi::firstWhere('id', $id);
        return view('sales.pages.call.detail', [
            'title' => 'Call Page',
            'active' => 'call',
            'active_sub' => 'call',
            "data" => $distribusi
        ]);
    }
    public function salesCallback(Request $request)
    {
        $data = Distribusi::where('user_id', $request->user_id)
            ->where('status', '2');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('statusText', function ($data) {
                switch ($data->status) {
                    case '2':
                        $statusText = 'Interest';
                        break;
                    case '3':
                        $statusText = 'Call Back';
                        break;
                }
                return $statusText;
            })
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'call/detail', 'type' => 'sales']);
            })
            ->editColumn('customer.no_telp', '{{{substr($customer[\'no_telp\'], 0, 6)}}}xxxx')
            ->make(true);
    }
    public function salescallStore(Request $request, $id)
    {
        $id = decrypt($id);
        $checkdata = ['id' => $id];

        $rules = [
            'status'     => ['required'],
            'deskripsi'  => ['required'],
        ];

        $validateData = $request->validate($rules);

        $validateData['end_call_time'] =  now();
        $validateData['updated_at'] =  now();

        Distribusi::updateOrInsert($checkdata, $validateData);
        Session::flash('success', 'Data Berhasil diupdate!');
        return redirect('/call');
    }
}
