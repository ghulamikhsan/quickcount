<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Energibangsa\Cepet\controllers\BaseController;
use Energibangsa\Cepet\helpers\EB;
use Illuminate\Http\Request;
use DataTables;
use DB;



class DashboardController extends BaseController
{
    public function getIndex(Request $request)
    {
        $users = DB::table('users')->count();
        $polings = DB::table('counts')->select('tps_name')->get()->count();
        $details = DB::table('counts')->select(DB::raw('SUM(count) as suara'))
                    ->first()->suara;
        $report = DB::table('poling_details')->select(DB::raw('SUM(count) as suara'))
                    ->first()->suara;
        $summary = DB::table('poling_details')->select(DB::raw('SUM(count) as suara'))
                    ->first()->suara;
        $charts = DB::table('counts')
                    ->join('calons', 'calons.id', '=', 'counts.calon_id')
                    ->select(DB::raw('SUM(count) as count'), 'calons.name as name')
                    ->groupBy('calons.name')
                    ->get();

        //pie chart
        $array[] = ['Nama', 'Suara'];
            foreach($charts as $key => $value)
            {
                 $array[++$key] = [$value->name, $value->count];
            }
        // dd(json_encode($charts));

        $data = [
            'title' => 'Dashboard',
            'users' => $users,
            'polings' => $polings,
            'details' => $details,
            'report' => $report,
            'summary' => $summary,
            'charts' => $charts,
        ];

        return view('dashboard.index', $data)->with('nama', json_encode($array));
    }

    public function dashboardTable(Request $request)
    {
            $data = DB::table('counts')
                    ->join('calons', 'calons.id', '=', 'counts.calon_id')
                    ->orderBy('tps_name')
                    ->get();
        

        return view('table.index', compact('data'));
    }

}