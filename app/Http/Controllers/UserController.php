<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Roleuser;
use App\Models\User;
use Illuminate\Http\Request;
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
        $data = User::where('status', '1');
        switch (auth()->user()->roleuser_id) {
            case '1':
                $data = $data->latest();
                break;
            default:
                $data = $data->where('roleuser_id', '3')
                    ->where('parentuser_id', auth()->user()->id)
                    ->latest();
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
        $userSelect = User::where('status', '1')
            ->where('roleuser_id', '2')
            ->get();
        $roleSelect = Roleuser::where('status', '1')
            ->get();
        $produkSelect = Produk::where('status', '1')
            ->get();
        //dd($userSelect);
        return view('admin.pages.user.form', [
            'title' => 'User',
            'active' => 'user',
            'active_sub' => '',
            "data" => '',
            "userSelect" => $userSelect,
            "roleSelect" => $roleSelect,
            "produkSelect" => $produkSelect,
        ]);
    }
    public function userEdit(User $user)
    {
        $userSelect = User::where('status', '1')
            ->where('roleuser_id', '2')
            ->get();
        $roleSelect = Roleuser::where('status', '1')
            ->get();
        $produkSelect = Produk::where('status', '1')
            ->get();
        return view('admin.pages.user.form', [
            'title' => 'User',
            'active' => 'user',
            'active_sub' => '',
            "data" => $user,
            "userSelect" => $userSelect,
            "roleSelect" => $roleSelect,
            "produkSelect" => $produkSelect,
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
        $upData = ['status' => '2'];
        $id = decrypt($request->id);

        //dd($upData);
        /** Update Call start time */
        User::where('id', $id)
            ->Update($upData);
        Session::flash('success', 'Data Berhasil Dihapus!');
        return redirect('/user');
    }
    public function userStore(Request $request, User $user)
    {
        $checkdata = ['id' => $user->id];

        $rules = [
            'name'      => ['required'],
            'username'  => ['required'],
            'email'     => ['required'],
            'password'  => ['required'],
        ];

        if (auth()->user()->roleuser_id == '1' && $request->roleuser_id == '3') {
            $rules['parentuser_id'] = 'required';
            $rules['roleuser_id'] = 'required';
            $rules['produk_id'] = 'required';
        }
        if (auth()->user()->roleuser_id == '1' && $request->roleuser_id == '2') {
            $rules['roleuser_id'] = 'required';
            $rules['produk_id'] = 'required';
        }

        if ($request->email != $user->email) {
            $rules['email'] = 'required|unique:users';
        }
        if ($request->username != $user->username) {
            $rules['username'] = 'required|unique:users';
        }
        $validateData = $request->validate($rules);
        $validateData['password'] = Hash::make($validateData['password']);

        if (auth()->user()->roleuser_id == '2') {
            $validateData['roleuser_id'] =  '3';
            $validateData['parentuser_id'] =  auth()->user()->id;
            $validateData['produk_id'] =  auth()->user()->produk_id;
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
