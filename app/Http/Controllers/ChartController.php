<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Count;
use DB;

class ChartController extends Controller
{
    public function index()
    {
        $charts = DB::table('counts')
                    ->join('calons', 'calons.id', '=', 'counts.calon_id')
                    ->select(DB::raw('SUM(count) as count'), 'calons.name as name')
                    ->groupBy('calons.name')
                    ->get();

        $array[] = ['name', 'count'];
            foreach($charts as $key => $value)
            {
                $array[++$key] = [$value->name, $value->count];
            }

        // dd(json_encode($array));
        return view('chart.google')->with('nama', json_encode($array));
    }

    public function google()
    {
         $data = DB::table('poling_details')
                    ->join('calons', 'calons.id', '=', 'poling_details.calon_id')
                    ->join('polings', 'polings.id', '=', 'poling_details.poling_id')
                    ->select('calons.name as nama', 'count', 'polings.tps_name as nama_tps')
                    ->get();
        $array[] = ['nama', 'count'];
            foreach($data as $key => $value)
            {
            $array[++$key] = [$value->nama, $value->count];
            }
        return view('chart.google')->with('nama', json_encode($array));
    }
}
