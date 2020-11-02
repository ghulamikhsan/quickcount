<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TPSAPIController extends Controller
{
    public function index()
    {
        $tps = DB::table('counts')
                    ->select('id','tps_name')
                    ->groupBy('id','tps_name')
                    ->paginate(10);

        foreach ($tps as $key => $tp) {
            $tps_details = DB::table('poling_details')
                        ->join('calons', 'calons.id', '=', 'poling_details.calon_id')
                        // ->join('polings', 'polings.id', '=', 'poling_details.poling_id')
                        ->select('calons.name as nama', 'count', 'calons.number')
                        ->where('poling_id', $tp->id)
                        ->get();
               
            // $tps_details = collect(['details'=>$tps_details]);
            $tps[$key]->details = $tps_details;
        }

        return response()->json([
            'message' => 'Detail retrieved',
            'status' => 1,
            'data' => [
                'tps' => $tps
                // 'nama' => $nama
                ]
        ]);
    }
    public function show($id)
    {
        $tps = DB::table('poling_details')
                    ->join('calons', 'calons.id', '=', 'poling_details.calon_id')
                    ->join('polings', 'polings.id', '=', 'poling_details.poling_id')
                    ->select('calons.name as name','polings.tps_name', 'count')
                    // ->groupBy('poling_id', 'calons.name')
                    ->where('poling_id', $id)
                    ->get();
        return response()->json([
            'message' => 'Detail retrieved',
            'status' => 1,
            'data' => [
                'tps' => $tps
                // 'nama' => $nama
                ]
        ]);
    }
    public function tpsName()
    {
        $tps = DB::table('polings')
                    ->select('polings.id as id_tps','tps_name')
                    ->get();
        return response()->json([
            'message' => 'Detail retrieved',
            'status' => 1,
            'data' => [
                'tps' => $tps
                // 'nama' => $nama
                ]
        ]);
    }
    
}
