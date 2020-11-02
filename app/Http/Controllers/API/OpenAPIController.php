<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseApiController;
use Energibangsa\Cepet\helpers\EB;

use DB;
use JWTAuth;

class OpenAPIController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {   
        return $this->output([
            'status' => 0,
            'message' => 'Not Found',
        ], 404);
    }

    public function getSlideshows()
    {
        $prefix = DB::getTablePrefix();

        $data = DB::table("slideshows")
                ->orderBy('orderable', 'asc')
                ->select('slideshows.*', DB::raw("CONCAT('".url("/storage")."/',image) as image"))
                ->get();

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function getPopup()
    {
        $data = DB::table('popups')
                    ->where('status', 1)
                    ->select('popups.*', DB::raw("CONCAT('".url("/storage")."/',image) as image"));
        if ($data->count() == 0) {
            return $this->output([
                'status' => 0,
                'message' => 'Popup tidak tersedia',
            ]);
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data->first(),
        ]);
    }
}
