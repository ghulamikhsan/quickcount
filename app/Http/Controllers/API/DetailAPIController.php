<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class DetailAPIController extends Controller
{
    public function index()
    {
        $details = DB::table('counts')->select(DB::raw('SUM(count) as suara'))
                    ->first()->suara;
        $calon = DB::table('counts')
                    ->Leftjoin('calons', 'calons.id', '=', 'counts.calon_id')
                    // ->Leftjoin('polings', 'polings.id', '=', 'poling_details.poling_id')
                    ->select(DB::raw('SUM(count) as count'), 'calons.name as name', 'number', 'calons.id')
                    ->groupBy('calons.name', 'number', 'calons.id')
                    ->orderBy('calons.number')
                    ->get();
                    
        return response()->json([
            'message' => 'Detail retrieved',
            'status' => 1,
            'data' => [
                'total_suara_keseluruhan' => $details,
                'calon' => $calon,
                // 'nama' => $nama
                ]
        ]);
    }
}
