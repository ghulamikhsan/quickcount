<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseApiController;
use Energibangsa\Cepet\helpers\EB;
use Auth;

use App\PolingDetail;
use DB;
use JWTAuth;

class PolingDetailsAPIController extends BaseApiController
{
    public function getAll()
    {
        $details = PolingDetail::latest()
                // ->where('user_id', Auth::user()->id)
                ->get();

        return response([
            'success'   => true,
            'message'   => 'List semua Calon',
            'data'      => $details
        ], 200);
    }

    public function postAdd()
    {
        $this->setValidator([
            'poling_id' => [
                'required' => 'Nama TPS wajib diisi',
            ],
            'calon_id' => [
                'required' => 'Nomor Urut Calon wajib diisi',
            ],
            'count' => [
                'required' => 'Jumlah Suara wajib diisi',
            ], 
        ]);
        
        $postdata = request()->all();

        $insert = EB::insert('poling_details', $postdata);

        return $this->output([
            'status' => 1,
            'message' => 'Data Detail Calon sukses di tambahkan',
            'data' => $postdata
        ]);
        if (!$insert) {
            return $this->output([
                'status' => 0,
                'message' => 'gagal memasukkan ke database',
            ]);
        }
    }

    public function postEdit(Request $request, $id)
    {
        $details = DB::table('poling_details')
            ->where('id', $id)
            ->update([
            'poling_id' => $request->poling_id,
            'calon_id' => $request->calon_id,
            'count' => $request->count,]);
        
        return response()->json([
            'status'   => true,
            'message'  => 'Data berhasil di update',
            'data'     => $details,
        ]);
    }

    public function postDelete($id)
    {
        $data = PolingDetail::find($id);
        $data->delete();

        return response()->json([
                'message' => 'Deleted data succesfully!',
                'status' => 1
            ]);
    }

    
}
