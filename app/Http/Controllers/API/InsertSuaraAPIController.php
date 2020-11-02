<?php

namespace App\Http\Controllers\API;

use Energibangsa\Cepet\helpers\EB;
use Energibangsa\Cepet\controllers\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Count;

use DB;
use JWTAuth;
use Request as Req;
use Auth;

class InsertSuaraAPIController extends BaseApiController
{
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function getAll()
    {
        $details = Count::latest()
                ->where('created_by', Auth::user()->id)
                ->get();

        return response([
            'success'   => true,
            'message'   => 'List semua Suara',
            'data'      => $details
        ], 200);
    }

    public function postAdd(Request $request)
    {
        $this->setValidator([
            'tps_name' => [
                'required' => 'Nama TPS wajib diisi',
            ],
            'count' => [
                'required' => 'Jumlah Suara wajib diisi',
            ], 
        ]);
        
        $postdata = request()->all();
         
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
        }

        foreach ($postdata['count'] as $key => $value) {
            DB::table('counts')->insert([
                'calon_id' => $key,
                'tps_name' => $postdata['tps_name'],
                'count' => $value,
                'photo' => $postdata['photo'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
            ]);
        }


        return response()->json([
            'status'    => true,
            'message'   => 'Suara di berhasil tambahkan',
            'data'  => $postdata,
        ]);

    }
}