<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Produk;
use App\Models\Roleuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    //
    public function index()
    {
        return view(
            'admin.pages.user.index',
            [
                'title' => 'User',
                'active' => 'user',
                'active_sub' => '',
                'data' => User::latest()->paginate(10)->withQueryString()
            ]
        );
    }
    public function dataTables()
    {
        $data = User::select(
            'users.*',
            DB::raw('date(users.join_date) as joindate'),
            DB::raw('date(users.resign_date) as resigndate'),
            'roleusers.nama as roletext',
            DB::raw('UPPER(IF(spv.name is not null, spv.name, users.name)) as spvnama'),
            DB::raw('UPPER(IF(spv.nickname is not null, spv.nickname, users.nickname)) as spvnickname'),
            'sm.name as smnama',
            'um.name as umnama',
        )
            ->join('roleusers', 'roleusers.id', '=', 'users.roleuser_id')
            ->leftjoin('users as spv', 'spv.id', '=', 'users.parentuser_id')
            ->leftjoin('users as sm', 'sm.id', '=', 'users.sm_id')
            ->leftjoin('users as um', 'um.id', '=', 'users.um_id');
        switch (auth()->user()->roleuser_id) {
            case '1':
                $data = $data->orderby('users.created_at', 'desc');
                break;
            case '4':
            case '6':
                $data = $data->where('users.roleuser_id', '<>', '1')
                    ->where('users.um_id', auth()->user()->id)
                    ->where('users.cabang_id', auth()->user()->cabang_id)
                    ->orderby('users.created_at', 'desc');
                break;

            case '5':
                $data = $data->where('users.roleuser_id', '<>', '1')
                    ->where('users.roleuser_id', '<>', '4')
                    ->where('users.roleuser_id', '<>', '5')
                    ->where('users.roleuser_id', '<>', '6')
                    ->where('users.sm_id', auth()->user()->id)
                    ->orderby('users.created_at', 'desc');
                break;
            default:
                $data = $data->where('users.roleuser_id', '3')
                    ->where('users.parentuser_id', auth()->user()->id)
                    ->orderby('users.created_at', 'desc');
                break;
        }
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('statusText', function ($data) {
                switch ($data->status) {
                    case '1':
                        $statusText = 'Active';
                        break;
                    case '2':
                        $statusText = 'Not Active';
                        break;
                    default:
                        $statusText = 'Not Active';
                        break;
                }
                return $statusText;
            })
            ->addColumn('action', function ($data) {
                return view('admin.layouts.buttonActiontables')
                    ->with(['data' => $data, 'links' => 'user', 'type' => 'all']);
            })
            ->make(true);
    }
    public function userFormadd()
    {
        $spvSelect = User::where('status', '1')
            ->where('roleuser_id', '2');
        $smSelect = User::where('status', '1')
            ->where('roleuser_id', '5');
        $umSelect = User::where('status', '1')
            ->where('roleuser_id', '6');

        $roleSelect = Roleuser::where('status', '1');
        $produkSelect = Produk::where('status', '1');
        $cabangSelect = Cabang::where('status', '1');

        if (auth()->user()->roleuser_id != '1') {
            $spvSelect = $spvSelect->where('cabang_id', auth()->user()->cabang_id);
            $smSelect = $smSelect->where('cabang_id', auth()->user()->cabang_id);
            $umSelect = $umSelect->where('cabang_id', auth()->user()->cabang_id);
            if (auth()->user()->roleuser_id == '5') {
                $spvSelect = $spvSelect->where('sm_id', auth()->user()->id);
                $smSelect = $smSelect->where('id', auth()->user()->id);
                $umSelect = $umSelect->where('id', auth()->user()->um_id);
                $roleSelect = $roleSelect->where('id', '<>', '1')
                    ->where('id', '<>', '4')
                    ->where('id', '<>', '5')
                    ->where('id', '<>', '6');
            } else if (auth()->user()->roleuser_id == '6') {
                $spvSelect = $spvSelect->where('um_id', auth()->user()->id);
                $smSelect = $smSelect->where('um_id', auth()->user()->id);
                $umSelect = $umSelect->where('id', auth()->user()->id);
                $roleSelect = $roleSelect->where('id', '<>', '1')
                    ->where('id', '<>', '4')
                    ->where('id', '<>', '6');
            } else {
                $roleSelect = $roleSelect->where('id', '<>', '1')
                    ->where('id', '<>', '4')
                    ->where('id', '<>', '6');
            }
        }
        // dd($spvSelect);
        return view('admin.pages.user.form', [
            'title' => 'User',
            'active' => 'user',
            'active_sub' => '',
            "data" => '',
            "spvSelect" => $spvSelect->get(),
            "smSelect" => $smSelect->get(),
            "umSelect" => $umSelect->get(),
            "roleSelect" => $roleSelect->get(),
            "produkSelect" => $produkSelect->get(),
            "cabangSelect" => $cabangSelect->get(),
        ]);
    }
    public function userEdit(User $user)
    {
        $spvSelect = User::where('status', '1')
            ->where('roleuser_id', '2');
        $smSelect = User::where('status', '1')
            ->where('roleuser_id', '5');
        $umSelect = User::where('status', '1')
            ->where('roleuser_id', '6');

        $roleSelect = Roleuser::where('status', '1');
        $produkSelect = Produk::where('status', '1');
        $cabangSelect = Cabang::where('status', '1');

        if (auth()->user()->roleuser_id != '1') {
            $spvSelect = $spvSelect->where('cabang_id', auth()->user()->cabang_id);
            $smSelect = $smSelect->where('cabang_id', auth()->user()->cabang_id);
            $umSelect = $umSelect->where('cabang_id', auth()->user()->cabang_id);
            if (auth()->user()->roleuser_id == '5') {
                $roleSelect = $roleSelect->where('id', '<>', '1')
                    ->where('id', '<>', '4')
                    ->where('id', '<>', '5')
                    ->where('id', '<>', '6');
            } else {
                $roleSelect = $roleSelect->where('id', '<>', '1')
                    ->where('id', '<>', '4')
                    ->where('id', '<>', '6');
            }
        }
        //dd($spvSelect);
        return view('admin.pages.user.form', [
            'title' => 'User',
            'active' => 'user',
            'active_sub' => '',
            "data" => $user,
            "spvSelect" => $spvSelect->get(),
            "smSelect" => $smSelect->get(),
            "umSelect" => $umSelect->get(),
            "roleSelect" => $roleSelect->get(),
            "produkSelect" => $produkSelect->get(),
            "cabangSelect" => $cabangSelect->get(),
        ]);
    }
    public function userShow(User $user)
    {
        return view('admin.pages.user.detail', [
            'title' => 'User',
            'active' => 'user',
            'active_sub' => '',
            "data" => $user
        ]);
    }
    public function userDestroy(Request $request)
    {
        if ($request->tipe == 'delete') {
            $upData = ['status' => '2'];
        } else if ($request->tipe == 'kehadiran') {
            if ($request->flag == '1') {
                $upData = ['flag_hadir' => date("Y-m-d")];
            } else {
                $upData = ['flag_hadir' => null];
            }
        }
        $id = decrypt($request->id);

        //dd($upData);
        /** Update Call start time */
        User::where('id', $id)
            ->Update($upData);
        if ($request->tipe == 'delete') {
            Session::flash('success', 'Data Berhasil Dihapus!');
        } else if ($request->tipe == 'kehadiran') {
            Session::flash('success', 'Data Berhasil Diupdate!');
        }
        return redirect('/user');
    }
    public function userStore(Request $request, User $user)
    {
        $checkdata = ['id' => $user->id];

        $rules = [
            'name'      => ['required'],
            'username'  => ['required'],
            'email'     => ['required'],
            'status'    => ['required'],
        ];
        if (auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '5' || auth()->user()->roleuser_id == '6') {

            $rules['roleuser_id'] = 'required';
            if ($request->roleuser_id != '1') {
                $rules['cabang_id'] = 'required';
            }
            if ($request->roleuser_id == '2' || $request->roleuser_id == '3' || $request->roleuser_id == '5' || $request->roleuser_id == '6') {
                $rules['produk_id'] = 'required';
                if ($request->roleuser_id == '2') {
                    $rules['sm_id'] = 'required';
                    $rules['um_id'] = 'required';
                }
                if ($request->roleuser_id == '3') {
                    $rules['parentuser_id'] = 'required';
                    $rules['sm_id'] = 'required';
                    $rules['um_id'] = 'required';
                }
                if ($request->roleuser_id == '5') {
                    unset($rules['produk_id']);
                    $rules['um_id'] = 'required';
                }
                if ($request->roleuser_id == '6') {
                    unset($rules['produk_id']);
                }
            }
        }
        if (auth()->user()->roleuser_id == '2') {
            $rules['roleuser_id'] = 'required';
            $rules['cabang_id'] = 'required';
            $rules['produk_id'] = 'required';
            $rules['parentuser_id'] = 'required';
        }

        // if ((auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4') && $request->roleuser_id == '3') {
        //     $rules['parentuser_id'] = 'required';
        //     $rules['roleuser_id'] = 'required';
        //     $rules['produk_id'] = 'required';
        // }
        // if ((auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4') && $request->roleuser_id == '2') {
        //     $rules['roleuser_id'] = 'required';
        //     $rules['produk_id'] = 'required';
        // }
        if (!isset($user->id)) {
            $rules['password'] = 'required';
        }
        if (isset($user->id)) {
            if ($request->password != '') {
                $rules['password'] = 'required';
            }
        }

        if ($request->email != $user->email) {
            $rules['email'] = 'required|unique:users';
        }
        if ($request->username != $user->username) {
            $rules['username'] = 'required|unique:users';
        }
        $validateData = $request->validate($rules);
        $validateData['refferal'] = $request->refferal;
        $validateData['salescode'] = $request->salescode;
        $validateData['join_date'] = $request->join_date;
        $validateData['resign_date'] = $request->resign_date;
        switch ($validateData['status']) {
            case 'Active':
                $validateData['status'] = '1';
                break;
            default:
                $validateData['status'] = '0';
                break;
        }
        if (!isset($user->id)) {
            $validateData['password'] = Hash::make($validateData['password']);
        }
        if (isset($user->id)) {
            if ($request->password != '') {
                $validateData['password'] = Hash::make($validateData['password']);
            }
        }
        if (auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4' || auth()->user()->roleuser_id == '5' || auth()->user()->roleuser_id == '6') {
            $validateData['nickname'] =  $request->nickname;
            if ($request->roleuser_id == '2') {
                $validateData['parentuser_id'] =  '0';
            }
            if ($request->roleuser_id == '4' || $request->roleuser_id == '5' || $request->roleuser_id == '6') {
                $validateData['parentuser_id'] =  '0';
                $validateData['produk_id'] =  '';
            }
        }

        // if (auth()->user()->roleuser_id == '2') {
        //     $validateData['roleuser_id'] =  '3';
        //     $validateData['parentuser_id'] =  auth()->user()->id;
        //     $validateData['produk_id'] =  auth()->user()->produk_id;
        //     $validateData['cabang_id'] =  auth()->user()->cabang_id;
        // }

        // if (auth()->user()->roleuser_id == '5') {
        //     $validateData['roleuser_id'] =  $request->roleuser_id;
        //     $validateData['parentuser_id'] =  $request->parentuser_id;
        //     $validateData['produk_id'] =  auth()->user()->produk_id;
        //     $validateData['cabang_id'] =  auth()->user()->cabang_id;
        // }

        if ((auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4') && ($request->roleuser_id == '1' || $request->roleuser_id == '4')) {

            $validateData['roleuser_id'] =  $request->roleuser_id;
            $validateData['parentuser_id'] =  '0';
            $validateData['produk_id'] =  '';
        }
        if (!isset($user->id)) {
            $validateData['email_verified_at'] =  now();
            $validateData['created_at'] =  now();
        }
        $validateData['updated_at'] =  now();

        User::updateOrInsert($checkdata, $validateData);
        if (isset($user->id)) {
            Session::flash('success', 'Data Berhasil diupdate!');
        } else {
            Session::flash('success', 'Data Berhasil ditambahkan!');
        }

        return redirect('/user');
    }
}
