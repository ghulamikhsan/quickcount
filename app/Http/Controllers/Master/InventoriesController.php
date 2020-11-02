<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

use Energibangsa\Cepet\helpers\EB;


class InventoriesController extends BaseController
{
    public function init()
    {
        $this->title      = 'Inventories';
        $this->tableName  = 'inventories';
        $this->pk         = 'id';
        $this->rawColumns = ['picts'];

        $this->trashBtn();

        $this->column('id', 'ID')->add();
        $this->column('inventories.name', 'Name')->add();
        $this->column('categories.name', 'Category', 'category_name')->add();
        $this->column('code', 'Code')->add();
        $this->column('picts', 'Picts')->add();
        $this->column('pv', 'Point')->add();
        $this->column('descriptions', 'Description')->add();

        $this->form('name', 'Name', 'text')
            ->validation([
                'required' => 'Input inventories name!',
            ])->add();
        $this->form('category_id', 'Category', 'select')
            ->options([
                'data' => DB::table('categories')->get(),
                'value' => 'id',
                'name' => 'name',
            ])
            ->validation([
                'required' => 'Harap isi Category',
            ])
            ->add();
        $this->form('code', 'Code', 'text')->add();
        $this->form('pv', 'Point', 'number')->add();
        $this->form('picts', 'Picts', 'file')->add();
        $this->form('descriptions', 'Descriptions', 'textarea')->add();
    }
    public function editQuery(&$query)
    {
        $query->leftJoin('categories', $this->tableName.".category_id", '=', 'categories.id')
            ->select($this->tableName.'.id',
                        'category_id',
                        'inventories.name as name',
                        'code',
                        'picts',
                        'descriptions',
                        'pv',
                        'categories.name as category_name');
    }

    public function beforeAdd(&$postdata) {
        $postdata['created_by'] = Auth::user()->id;
    }
    public function afterDelete(&$id) {
        DB::table('inventories')->where('id', $id)->update([
            'deleted_by' => Auth::user()->id,
        ]);
    }

    public function editDataTable(&$dataTable) {
        $dataTable->editColumn('picts', function($data) {
            return "<img src='".url("/storage/".$data->picts)."' alt='Gambar Product' width=150 height=150>";
        })->rawColumns(['picts']);
    }

    public function views($code = null)
    {
        if (!$code) {
            return abort(404);
        }

        $inventory = DB::table('inventories')->where('code', $code);

        if ($inventory->count() == 0) {
            return abort(404);
        }

        $data = (array) $inventory->first();
        $data['picts'] = EB::getImage($data['picts']);

        return view('products.detail', $data);
    }
}
