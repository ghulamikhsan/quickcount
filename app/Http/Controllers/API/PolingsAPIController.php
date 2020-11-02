<?php

namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
use Energibangsa\Cepet\controllers\BaseApiController;
use Energibangsa\Cepet\helpers\EB;
use Illuminate\Http\Request; 
use Auth;

// use Request;
use App\Poling;
use DB;
use JWTAuth;
use Request as Req;

class PolingsAPIController extends BaseApiController
{
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function getAll()
    {
        $details = Poling::latest()
                ->where('created_by', Auth::user()->id)
                ->get();

        return response([
            'success'   => true,
            'message'   => 'List semua TPS',
            'data'      => $details
        ], 200);
    }

    public function postAdd(Request $request)
    {
        
        $postdata = request()->all();
        // $postdata = DB::table('polings')
        //                 ->join('polings.id','=', 'poling_details.poling_id')
        //                 ->select('poling_details.count', 'tps_name', 'photo')
        //                 ->get();
                    
         
        //update foto form C1
        $postdata['updated_at'] = date('Y-m-d H:i:s');
        $postdata['created_at'] = date('Y-m-d H:i:s');
        $postdata['created_by'] = Auth::user()->id;
        
        if (Req::file('photo')) {
            $upload = EB::uploadFile('photo', 'public/polings', null, ['allowed_type' => 'jpeg|png|jpg'] );
            // dd($upload);
            if (!$upload['status']) {
                return $this->output([
                    'status' => 0,
                    'message' => $upload['message'],
                ]);
            } else {
                $postdata['photo'] = $upload['filename'];
            }
            $polings = DB::table('polings')
                        ->insert($postdata);                
        }


        return response()->json([
            'status'    => true,
            'message'   => 'Data TPS di berhasil tambahkan',
            'data'  => $postdata,
        ]);

    }

    public function postEdit(Request $request, $id)
    {
        $details = DB::table('polings')
            ->where('id', $id)
            ->update([
            'tps_name' => $request->tps_name,
            'photo' => $request->photo,
            'region_name' => $request->region_name,]);

        $postdata = request()->all();

        return response()->json([
            'status'   => true,
            'message'  => 'Data berhasil di update',
            'data'     => $postdata,
        ]);
    }

    public function postDelete($id)
    {
        $data = Poling::find($id);
        $data->delete();

        return response()->json([
                'message' => 'Deleted data succesfully!',
                'status' => 1
            ]);
    }
}
