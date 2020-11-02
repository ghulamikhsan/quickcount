<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

class PopupsController extends BaseController
{
    public function init()
    {
        $this->title        = 'Popup';
        $this->tableName    = 'popups';
        $this->pk           = 'id';
        $this->rawColumns   = ['image'];

        $this->trashBtn();

        $this->column('id', 'ID')->add();
        $this->column('image', 'Gambar')->add();
        $this->column('url', 'URL')->add();
        $this->column('status', 'Status')->add();

        $this->form('image', 'Gambar', 'file')
            ->validation([
                'required' => 'Gambar wajib diisi',
            ])->add();
        $this->form('url', 'URL', 'text')
            ->add();
        $this->form('status', 'Status', 'select')
            ->options([
                'data' => [
                    'Tidak Aktif', 
                    'Aktif'
                ]
                ])
            ->add();
    }

    public function editDataTable(&$dataTable) {
        $dataTable->editColumn('image', function($data) {
            return "<img src='".url("/storage/".$data->image)."' alt='Gambar Popup' width=150 height=150>";
        });

        $dataTable->editColumn('status', function($data) {
            return $data->status == 1 ? "Aktif" : "Tidak Aktif";
        });
    }

    public function afterAdd(&$postdata, &$id) {
        if ($postdata['status'] == 1) {
            DB::table($this->tableName)
                ->where('id', '<>', $id)
                ->update([
                    'status' => 0
                ]);
        }
    }

    public function afterEdit(&$postdata, &$id) {
        if ($postdata['status'] == 1) {
            DB::table($this->tableName)
                ->where('id', '<>', $id)
                ->update([
                    'status' => 0
                ]);
        }
    }
}
