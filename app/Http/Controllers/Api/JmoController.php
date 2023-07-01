<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\JmoResource;
use App\Http\Controllers\Controller;
use App\Models\Jmo;
use Illuminate\Support\Facades\Hash;

class JmoController extends Controller
{
    public function index(Request $request)
    {
        //
    }
    public function store(Request $request)
    {
        if ($request->token == 'SIPPASKIT') {
            //get all posts
            $statusData = false;
            $jmo = Jmo::where('email', $request->email)->get();
            //dd($jmo);
            foreach ($jmo as $item) {
                if (!Hash::check($request->password, $item->password)) {
                    $statusData = false;
                    $jmoData = [];
                } else {
                    $statusData = true;

                    $cardpath = $item->cardpath == '' || $item->cardpath == null ? 'card.png' : $item->cardpath;

                    $jmoData = [
                        'noKartu' => $item->nokartu,
                        'statusPeserta' => $item->statusPeserta,
                        'segmenPeserta' => ucwords(strtolower($item->segmenPeserta)),
                        'perusahaan' => strtoupper($item->perusahaan),
                        'lastUpah' => number_format($item->lastUpah, 2, ",", "."),
                        'lastIuranDate' => ucwords(strtolower($item->lastIuranDate)),
                        'pensiunanDate' => ucwords(strtolower($item->pensiunanDate)),
                        'masaIuranjp' => $item->masaIuranjp,
                        'kepesertaanDate' => ucwords(strtolower($item->kepesertaanDate)),
                        'masaIuranjkp' => $item->masaIuranjkp,
                        'jkm' => $item->jkm,
                        'jkk' => $item->jkk,
                        'jht' => $item->jht,
                        'jp' => $item->jp,
                        'jkp' => $item->jkp,
                        'cardpath' => substr(strrchr(rtrim($cardpath, '/'), '/'), 1),
                    ];
                }
            }
            //return collection of posts as a resource
            return new JmoResource($statusData, 'List Data Posts', $jmoData);
            // return new JmoResource($statusData, 'List Data Posts', $jmo);
        }
    }
}
