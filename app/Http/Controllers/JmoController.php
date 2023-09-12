<?php

namespace App\Http\Controllers;

use App\Models\Jmo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class JmoController extends Controller
{
    //
    public function index()
    {
        return view(
            'admin.pages.jmosip.index',
            [
                'title' => 'Jmosip',
                'active' => 'jmosip',
                'active_sub' => 'list',
                'data' => Jmo::latest()->paginate(10)->withQueryString()
            ]
        );
    }
    public function dataTables()
    {
        $data = Jmo::latest();
        // switch (auth()->user()->roleuser_id) {
        //     case '1':
        //         $data = $data->latest();
        //         break;
        //     default:
        //         $data = $data->where('roleuser_id', '3')
        //             ->where('parentuser_id', auth()->user()->id)
        //             ->latest();
        //         break;
        // }
        return DataTables::of($data)
            ->addIndexColumn()
            // ->addColumn('statusText', function ($data) {
            //     switch ($data->status) {
            //         case '1':
            //             $statusText = 'Active';
            //             break;
            //         case '2':
            //             $statusText = 'Not Active';
            //             break;
            //         default:
            //             $statusText = 'Not Active';
            //             break;
            //     }
            //     return $statusText;
            // })
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'jmosip', 'type' => 'all']);
            })
            ->make(true);
    }
    public function jmoFormadd()
    {
        //dd($userSelect);
        return view('admin.pages.jmosip.form', [
            'title' => 'Jmosip',
            'active' => 'jmosip',
            'active_sub' => '',
            "data" => '',
        ]);
    }
    public function jmoEdit($id)
    {
        $id = decrypt($id);
        $jmo = Jmo::firstWhere('id', $id);
        return view('admin.pages.jmosip.form', [
            'title' => 'Jmosip',
            'active' => 'jmosip',
            'active_sub' => '',
            "data" => $jmo,
        ]);
    }

    public function jmoStore(Request $request, Jmo $jmo)
    {

        $checkdata = ['id' => $jmo->id];

        $rules = [
            'nama'      => ['required'],
            'nokartu'  => ['required'],
            'email'     => ['required'],
            'perusahaan'     => ['required'],
        ];

        if ($request->email != $jmo->email) {
            $rules['email'] = 'required|unique:users';
        }

        if (!isset($jmo->id)) {
            $rules['password'] = 'required';
        }
        if (isset($jmo->id)) {
            if ($request->password != '') {
                $rules['password'] = 'required';
            }
        }

        $validateData = $request->validate($rules);

        if (!isset($jmo->id)) {
            $validateData['password'] = Hash::make($validateData['password']);
        }
        if (isset($jmo->id)) {
            if ($request->password != '') {
                $validateData['password'] = Hash::make($validateData['password']);
            }
        }

        $validateData['statuspeserta'] =  '1';
        $validateData['nik'] =  $request->nik;
        $validateData['no_telp'] =  $request->no_telp;
        $validateData['jmltenagakerja'] =  $request->jmltenagakerja;
        $validateData['saldo'] =  $request->saldo;
        $validateData['lastiuran'] =  $request->lastiuran;
        $validateData['lastIuranDate_saldo'] =  $request->lastIuranDate_saldo;
        $validateData['segmenpeserta'] =  $request->segmenPeserta;
        $validateData['lastUpah'] =  $request->lastUpah;
        $validateData['lastIuranDate'] =  $request->lastIuranDate;
        $validateData['pensiunanDate'] =  $request->pensiunanDate;
        $validateData['masaIuranjp'] =  $request->masaIuranjp;
        $validateData['kepesertaanDate'] =  $request->kepesertaanDate;
        $validateData['masaIuranjkp'] =  $request->masaIuranjkp;
        $validateData['jkm'] =  (isset($request->jkm)) ? '1' : '0';
        $validateData['jkk'] =  (isset($request->jkk)) ? '1' : '0';
        $validateData['jht'] =  (isset($request->jht)) ? '1' : '0';
        $validateData['jp'] =  (isset($request->jp)) ? '1' : '0';
        $validateData['jkp'] =  (isset($request->jkp)) ? '1' : '0';


        if (!isset($jmo->id)) {
            $validateData['created_at'] =  now();
        }
        $validateData['updated_at'] =  now();

        //dd($request->file('cardpath'));
        if ($request->file('cardpath') != '') {
            if (isset($jmo->id)) {
                Storage::disk('public_uploads')->delete($jmo->cardpath);
            }
            /** Hosting upload */
            $imagePath = Storage::disk('public_uploads_hosting')->put('jmo', $request->file('cardpath'));

            // /** Local upload */
            //$imagePath = Storage::disk('public_uploads')->put('jmo', $request->file('cardpath'));
            $validateData['cardpath'] =  $imagePath;
            //dd($validateData['cardpath']);
            //exit;
        } else {
            if (!isset($jmo->id)) {
                $validateData['cardpath'] =  'jmo/card.png';
            }
        }
        if ($request->file('foto') != '') {
            if (isset($jmo->id)) {
                if ($jmo->foto != null) {
                    Storage::disk('public_uploads')->delete($jmo->foto);
                }
            }
            /** Hosting upload */
            $imagePath = Storage::disk('public_uploads_hosting')->put('foto', $request->file('foto'));

            // /** Local upload */
            //$imagePath = Storage::disk('public_uploads')->put('foto', $request->file('foto'));
            $validateData['foto'] =  $imagePath;
            //dd($validateData['cardpath']);
            //exit;
        } else {
            if (!isset($jmo->id)) {
                $validateData['foto'] =  'foto/default.jpg';
            }
        }

        Jmo::updateOrInsert($checkdata, $validateData);
        if (isset($jmo->id)) {
            Session::flash('success', 'Data Berhasil diupdate!');
        } else {
            Session::flash('success', 'Data Berhasil ditambahkan!');
        }

        return redirect('/jmosip');
    }
}
