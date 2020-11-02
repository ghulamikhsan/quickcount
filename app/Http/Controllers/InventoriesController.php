<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

class InventoriesController extends BaseController
{
    public function init()
    {
        $this->title     = 'Inventories';
        $this->tableName = 'inventories';
        $this->pk        = 'id';

        $this->trashBtn();

        $this->column('id', 'ID')->add();
        $this->column('inventories.name', 'Name')->add();
        $this->column('category_name', 'Category')->add();
        $this->column('code', 'Code')->add();
        $this->column('picts', 'Picts')->add();
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
}
