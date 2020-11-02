<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Count;
use DataTables;
use DB;

class TableController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('counts')
                    ->join('calons', 'calons.id', '=', 'counts.calon_id')
                    // ->select(['calons.name as name', 'tps_name', 'counts'])
                    ->orderBy('tps_name')
                    ->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                           $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
     
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('table.index');
       
    }
}
