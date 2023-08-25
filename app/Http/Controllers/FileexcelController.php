<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Fileexcel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreFileexcelRequest;
use App\Http\Requests\UpdateFileexcelRequest;

class FileexcelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view(
            'admin.pages.campaign.index',
            [
                'title' => 'Campaign',
                'active' => 'campaign',
                'active_sub' => '',
                'data' => ''
            ]
        );
    }
    public function dataTables()
    {
        $data = Fileexcel::latest();
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
            // ->addColumn('action', function ($data) {
            //     return view('admin.layouts.buttonActiontables')
            //         ->with(['data' => $data, 'links' => 'mutasi', 'type' => 'all']);
            // })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFileexcelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Fileexcel $fileexcel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fileexcel $fileexcel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileexcelRequest $request, Fileexcel $fileexcel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fileexcel $fileexcel)
    {
        //
    }
}
