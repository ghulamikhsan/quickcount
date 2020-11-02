<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseApiController;
use Energibangsa\Cepet\helpers\EB;

use App\Calon;
use DB;
use JWTAuth;

class CalonsAPIController extends BaseApiController
{
    public function getIndex()
    {
        return $this->output([
                'message' => 'Not Found',
                'status' => 0
            ], 404);
    }

    public function postAdd()
    {
        $this->setValidator([
            'name' => [
                'required' => 'Nama wajib diisi',
            ],
            'number' => [
                'required' => 'Nomor Urut Calon wajib diisi',
            ],
        ]);
        
        $postdata = request()->all();

        $insert = EB::insert('calons', $postdata);

        return $this->output([
            'status' => 1,
            'message' => 'Data Calon sukses di tambahkan',
            'data' => $postdata
        ]);
        if (!$insert) {
            return $this->output([
                'status' => 0,
                'message' => 'gagal memasukkan ke database',
            ]);
        }

    }

    public function getAll()
    {
        
        $calons = DB::table('calons')
                    ->orderBy('id')
                    ->get();

        return response([
            'success'   => true,
            'message'   => 'List semua Calon',
            'data'      => $calons
        ], 200);
    }

    public function postDelete($id)
    {
        $data = Calon::find($id);
        $data->delete();

        return response()->json([
                'message' => 'Deleted data succesfully!',
                'status' => 1
            ]);
    }

    public function postEdit(Request $request,$id)
    {
         $calons = DB::table('calons')
            ->where('id', $id)
            ->update([
            'name' => $request->name,
            'number' => $request->number,]);
        
        return response()->json([
            'status'   => true,
            'message'  => 'Data berhasil di update',
            'data'     => $calons,
        ]);
    }
}
