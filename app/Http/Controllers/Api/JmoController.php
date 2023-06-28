<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JmoResource;
use Illuminate\Http\Request;

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
            $jmoData = [
                'statusPeserta' => '1',
                'segmenPeserta' => 'Penerima Upah',
                'perusahaan' => 'BANK CTBC INDONESIA',
                'lastUpah' => 'Rp 7.500.000,00',
                'lastIuranDate' => '23 Juni 2023',
                'pensiunanDate' => '30 Agustus 2058',
                'masaIuranjp' => '5 Bulan',
                'kepesertaanDate' => '01 Februari 2023',
                'masaIuranjkp' => '4 Bulan',
                'jkm' => '1',
                'jkk' => '1',
                'jht' => '0',
                'jp' => '0',
                'jkp' => '1',
            ];

            //return collection of posts as a resource
            return new JmoResource(true, 'List Data Posts', $jmoData);
        }
    }
}
