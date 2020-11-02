<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

class SlideshowsController extends BaseController
{
    public function init()
    {
        $this->title        = 'Slideshows';
        $this->tableName    = 'slideshows';
        $this->pk           = 'id';
        $this->rawColumns   = ['image'];

        $this->trashBtn();

        $this->column('id', 'ID')->add();
        $this->column('image', 'Gambar')->add();
        $this->column('orderable', 'Urutan')->add();

        $this->form('image', 'Gambar', 'file')
            ->validation([
                'required' => 'Gambar wajib dimasukkan',
            ])->add();
        $this->form('orderable', 'Urutan', 'number')
            ->validation([
                'required' => 'Masukkan Urutan slideshow',
            ])->add();
    }

    public function editQuery(&$query)
    {
        $query->orderBy('orderable', 'asc');
    }

    public function editDataTable(&$dataTable) {
        $dataTable->editColumn('image', function($data) {
            return "<img src='".url("/storage/".$data->image)."' alt='Gambar Slideshows' width=300 height=150>";
        });
    }
}
