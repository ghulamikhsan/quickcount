<?php

namespace App\Http\Controllers;

use Energibangsa\Cepet\controllers\BaseController;

use DB;
use Hash;
use Request;
use Auth;

class PolingController extends BaseController
{
    public function init()
    {
        $this->title     = 'Poling';
        $this->tableName = 'polings';
        $this->pk        = 'id';
        $this->rawColumns = ['photo'];

        $this->trashBtn();
        
        // Initiate Column
        $this->column('id', 'ID')->add();
        $this->column('polings.tps_name', 'Nama TPS')->add();
        $this->column('photo', 'Foto Form C1')->add();
        $this->column('region_name', 'Nama Wilayah / Daerah')->add();
        $this->column('users.name', 'Dibuat Oleh', 'created_by')->add();

        $this->form('tps_name' , 'Nama TPS', 'text')
            ->validation([
                'required' => 'Harap isi Nama TPS',
            ])
            ->add();

                
                $this->form('photo', 'Foto C1', 'file')
                ->validation([
                    'required' => 'Harap isi Foto Form C1',
                ])
                ->add();
                $this->form('region_name', 'Nama Wilayah / Daerah', 'text')->add();
            }

        public function beforeAdd(&$postdata) 
        {
                $postdata['created_by'] = Auth::user()->id;
        }

        public function editDataTable(&$dataTable) {
            $dataTable->editColumn('photo', function($data) {
                return "<img src='".url("/storage/".$data->photo)."' alt='Gambar Product' width=150 height=150>";
            })->rawColumns(['photo']);
        }
    
        public function views($code = null)
        {
            if (!$code) {
                return abort(404);
            }
    
            $inventory = DB::table('polings')->where('code', $code);
    
            if ($inventory->count() == 0) {
                return abort(404);
            }
    
            $data = (array) $inventory->first();
            $data['photo'] = EB::getImage($data['photo']);
    
            return view('products.detail', $data);
        }

        public function editQuery(&$query)
        {
            $query
                    ->join('users', $this->tableName.".created_by", '=', 'users.id')
                    ->select($this->tableName.'.id',
                            'tps_name',
                            'photo',
                            'region_name',
                            'users.name as created_by');
        }
}
