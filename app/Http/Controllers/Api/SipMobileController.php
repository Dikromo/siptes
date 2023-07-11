<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Distribusi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\SipMobileResource;

class SipMobileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {

        $statusData = false;
        $user_id = '';
        $jmoData = [];
        if ($request->token == 'SIPPASKIT') {
            //get all posts
            $jmo = User::where('username', $request->email)->get();
            //dd($jmo);
            foreach ($jmo as $item) {
                if (!Hash::check($request->password, $item->password)) {
                    $statusData = false;
                    $jmoData = [];
                } else {
                    $statusData = true;
                    $user_id = $item->id;
                    $jmoData = [
                        'id' => $item->id,
                        'nama' => $item->name,
                    ];
                    //
                }
            }
            $jmoData['calldata'] = $this->calldata($user_id);
            //return collection of posts as a resource
            // return new JmoResource($statusData, 'List Data Posts', $jmo);
        }

        return new SipMobileResource($statusData, 'List Data', $jmoData);
    }
    private function calldata($id)
    {
        $result = [];
        $z = 0;
        $data = Distribusi::where('user_id', $id)
            ->where('status', 0)
            ->get();
        foreach ($data as $item) {
            if ($z == 0) {
                $result['list'] = [
                    'id' => $item->id,
                    'nama' => $item->customer->nama,
                    'no_telp' => $item->customer->no_telp,
                ];
            }
            $z++;
        }
        $result['totaldata'] = $data->count();
        return $result;
    }

    private function calldata_back($id)
    {
        $result = [];
        $z = 0;
        $data = Distribusi::where('user_id', $id)
            ->where('status', 3)
            ->get();
        foreach ($data as $item) {
            $result['list'][$z] = [
                'id' => $item->id,
                'nama' => $item->customer->nama,
                'no_telp' => $item->customer->no_telp,
                'status' => $item->status,
                'deskripsi' => $item->deskripsi,
                'call_date' => $item->updated_at,
            ];
            $z++;
        }
        $result['totaldata'] = $data->count();
        return $result;
    }
    public function refreshcalldata_back(Request $request)
    {
        $statusData = false;
        $jmoData = [];
        if ($request->token == 'SIPPASKIT') {
            $user_id = $request->user_id;
            $statusData = true;
            $jmoData['calldata'] = $this->calldata_back($user_id);
        }

        return new SipMobileResource($statusData, 'List Data', $jmoData);
    }
    public function refreshcalldata(Request $request)
    {
        $statusData = false;
        $jmoData = [];
        if ($request->token == 'SIPPASKIT') {
            $user_id = $request->user_id;
            $statusData = true;
            $jmoData['calldata'] = $this->calldata($user_id);
        }

        return new SipMobileResource($statusData, 'List Data', $jmoData);
    }

    public function savecalldata(Request $request)
    {
        $statusData = false;
        $jmoData = [];
        if ($request->token == 'SIPPASKIT') {
            $id = $request->id;
            $statusData = true;

            $checkdata = ['id' => $id];
            $validateData['status'] =  $request->status;
            $validateData['deskripsi'] =  $request->deskripsi;
            $validateData['end_call_time'] =  now();
            $validateData['updated_at'] =  now();
            Distribusi::updateOrInsert($checkdata, $validateData);
        }

        return new SipMobileResource($statusData, 'List Data', $jmoData);
    }
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
