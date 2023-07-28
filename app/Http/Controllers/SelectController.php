<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    public function getProdukspv(Request $request)
    {
        $result = '';
        $data = User::where('id', $request->id)
            ->get();
        foreach ($data as $item) {
            $result = $item->produk_id;
        }
        return json_encode($result);
    }
}
