<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CountAPIController extends Controller
{
    public function index()
    {
        $tps = DB::table('counts')
                    ->select('id','tps_name')
                    ->groupBy('id','tps_name')
                    ->paginate(10);
                    // ->get();

        foreach ($tps as $key => $tp) {
            $tps_details = DB::table('counts')
                        ->join('calons', 'calons.id', '=', 'counts.calon_id')
                        // ->join('polings', 'polings.id', '=', 'poling_details.poling_id')
                        ->select('calons.name','calons.number','count')
                        ->groupBy('calons.name','calons.number','tps_name', 'count')
                        ->where('tps_name', $tp->tps_name)
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
}
