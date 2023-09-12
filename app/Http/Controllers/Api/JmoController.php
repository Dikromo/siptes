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
            $note = [];
            //dd($jmo);
            foreach ($jmo as $item) {
                if (!Hash::check($request->password, $item->password)) {
                    $statusData = false;
                    $jmoData = [];
                } else {
                    $statusData = true;

                    $cardpath = $item->cardpath == '' || $item->cardpath == null ? 'card.png' : $item->cardpath;
                    $foto = $item->foto == '' || $item->foto == null ? 'default.jpg' : $item->foto;
                    if ($item->jht == '1') {
                        $note[] = 'JHT';
                    }
                    if ($item->jkk == '1') {
                        $note[] = 'JKK';
                    }
                    if ($item->jkm == '1') {
                        $note[] = 'JKM';
                    }
                    if ($item->jp == '1') {
                        $note[] = 'JP';
                    }
                    if ($item->jkp == '1') {
                        $note[] = 'JKP';
                    }
                    $note = implode(", ", $note);

                    $jmoData = [
                        'nik' => $item->nik,
                        'no_telp' => $item->no_telp,
                        'nama' => $item->nama,
                        'noKartu' => $item->nokartu,
                        'statusPeserta' => $item->statusPeserta,
                        'segmenPeserta' => ucwords(strtolower($item->segmenPeserta)),
                        'perusahaan' => strtoupper($item->perusahaan),
                        'lastUpah' => number_format($item->lastUpah, 2, ",", "."),
                        'saldo' => 'Rp ' . number_format($item->saldo, 2, ",", "."),
                        'lastiuran' => ucwords(strtolower($item->lastiuran)),
                        'lastIuranDate_saldo' => ucwords(strtolower($item->lastIuranDate_saldo)),
                        'jmltenagakerja' => ucwords(strtolower($item->jmltenagakerja)),
                        'lastIuranDate' => ucwords(strtolower($item->lastIuranDate)),
                        'pensiunanDate' => ucwords(strtolower($item->pensiunanDate)),
                        'masaIuranjp' => $item->masaIuranjp . ' Bulan',
                        'kepesertaanDate' => ucwords(strtolower($item->kepesertaanDate)),
                        'masaIuranjkp' => $item->masaIuranjkp . ' Bulan',
                        'jkm' => $item->jkm,
                        'jkk' => $item->jkk,
                        'jht' => $item->jht,
                        'jp' => $item->jp,
                        'jkp' => $item->jkp,
                        'note' => $note,
                        'cardpath' => substr(strrchr(rtrim($cardpath, '/'), '/'), 1),
                        'foto' => substr(strrchr(rtrim($foto, '/'), '/'), 1),
                    ];
                }
            }
            //return collection of posts as a resource
            return new JmoResource($statusData, 'List Data Posts', $jmoData);
            // return new JmoResource($statusData, 'List Data Posts', $jmo);
        }
    }
}
