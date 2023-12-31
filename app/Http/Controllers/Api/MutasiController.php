<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JmoResource;
use App\Models\Mutasi;
use App\Models\Mutasi_list;
use Illuminate\Http\Request;

class MutasiController extends Controller
{
    public function login(Request $request)
    {
        $statusData = false;
        $resultdata = [];
        if ($request->token == 'SIPPASKIT') {
            $statusData = true;
            $z = 0;
            $data = Mutasi::where('status', '1')
                ->where('pin', $request->pin)
                ->get();
            if ($data->count() > 0) {
                foreach ($data as $item) {
                    $resultdata = [
                        'id' => strval($item->id),
                        'nama' => $item->nama,
                        'norek' => $item->norek,
                        'pin' => $item->pin,
                        'pin2' => $item->pin2,
                    ];
                }
            } else {
                $statusData = false;
                $resultdata['list_data'] = [];
            }
        }

        return new JmoResource($statusData, 'List Data', $resultdata);
    }
    public function mutasi(Request $request)
    {
        $hariini = date('d/m/Y H:i:s');
        $daritgl = date('d/m/Y', strtotime(str_replace(' ', '', $request->dari)));
        $sampetgl = date('d/m/Y', strtotime(str_replace(' ', '', $request->sampe)));

        $daritglquery = date('Y-m-d', strtotime(str_replace(' ', '', $request->dari)));
        $sampetglquery = date('Y-m-d', strtotime(str_replace(' ', '', $request->sampe)));

        switch ($request->jenis) {
            case 'Semua':
                break;

            default:
                # code...
                break;
        }

        $periode = $daritgl . '-' . $sampetgl;
        $statusData = false;
        $resultdata = [];
        if ($request->token == 'SIPPASKIT') {
            $statusData = true;
            $z = 0;
            $data = Mutasi_list::where('mutasi_id', $request->id)
                ->whereDate('tanggal', '>=', $daritglquery)
                ->whereDate('tanggal', '<=', $sampetglquery)
                ->orderBy('tanggal', 'desc');
            switch ($request->jenis) {
                case 'Uang Masuk':
                    $resultdata['jenis'] = 'Uang Masuk';
                    $data = $data->where('jenis', 'CR');
                    break;

                case 'Uang Keluar':
                    $resultdata['jenis'] = 'Uang Keluar';
                    $data = $data->where('jenis', 'DB');
                    break;

                default:
                    $resultdata['jenis'] = 'Semua';
                    break;
            }
            $resultdata['tanggalinquery'] = $hariini;
            $resultdata['periode'] = $periode;
            if ($data->get()->count() > 0) {
                foreach ($data->get() as $item) {
                    $resultdata['list_data'][$z] = [
                        'id' => strval($item->id),
                        'jenis' => $item->jenis,
                        'deskripsi' => $item->deskripsi == null ? '' : $item->deskripsi,
                        'deskripsi2' => $item->deskripsi2 == null ? '' : $item->deskripsi2,
                        'deskripsi3' => $item->deskripsi3 == null ? '' : $item->deskripsi3,
                        'jumlah' => number_format($item->jumlah, 2, ".", ","),
                        'tanggal' => date('d/m', strtotime($item->tanggal)),
                    ];
                    $z++;
                }
            } else {
                $statusData = false;
                $resultdata['list_data'] = [];
            }
        }

        return new JmoResource($statusData, 'List Data', $resultdata);
    }
}
