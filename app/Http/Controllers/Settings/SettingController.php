<?php

namespace App\Http\Controllers\settings;

use Energibangsa\Cepet\controllers\BaseController;
use Energibangsa\Cepet\helpers\EB;

use DB;
use Request;

class SettingController extends BaseController
{
    public function init()
    {
        $this->title     = 'General Settings';
        $this->tableName = 'settings';
        $this->pk        = 'id';
    }

    public function getIndex()
    {
        $this->init();
        $data = [
            'forms' => DB::table($this->tableName)->get(),
            'title' => $this->title,
        ];

        return view("settings.generals.index", $data);
    }

    public function postSave()
    {
        if (Request::ajax()) {
            foreach (Request::input('name') as $key => $value) {
                EB::update('settings', ['value' => $value], ['id' => $key]);
            }
            
            return $this->output([
                'status' => 1,
                'message' => 'Sukses Menyimpan'
                ]);
        }
        
        return abort(404);
    }

    public function getAdd()
    {
        $data = [
            'title' => 'Tambah Setting',
        ];

        return view('settings.generals.add', $data);
    }

    public function postAdd()
    {
        if(EB::insert('settings', [
            'name' => Request::input('name'),
            'value' => Request::input('value')
        ])) {
            $url = rtrim(url()->current(), Request::segment(count(Request::segments())));
            return redirect($url);
        } else {
            return redirect(url()->current());
        }
    }
    
    public function postDelete()
    {
        $this->init();

        $id = Request::input('id');
        DB::beginTransaction();

        try {
            $delete = DB::table($this->tableName)->where($this->pk, '=', $id);
            $result = EB::delete($this->tableName, $delete);

            if($result){
                DB::commit();
                $res['status']  = 1;
                $res['message'] = 'Data berhasil dihapus.';
            }else{
                DB::rollBack();
                $res['status']  = 0;
                $res['message'] = 'Mengalami masalah.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $res['status']  = 0;
            $res['message'] = $e->getMessage();
        }
        
        return response()->json($res);
    }

    public function getTest()
    {
        return 'aw';
    }
}
