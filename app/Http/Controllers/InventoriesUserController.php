<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

class InventoriesUserController extends BaseController
{
    public function init()
    {
        $this->title     = 'Inventories User';
        $this->tableName = 'inventories_users';
        $this->pk        = 'id';

        $this->trashBtn();

        $this->column('id', 'ID')->add();
        $this->column('user_name', 'Name')->add();
        $this->column('inventory_name', 'Product Name')->add();
        $this->column('price', 'Price')->add();
        $this->column('stocks', 'Stock')->add();

        $this->form('user_id', 'Name', 'select')
            ->options([
                'data' => DB::table('users')->get(),
                'name' => 'name',
                'value' => 'id'
            ])
            ->validation([
                'required' => 'Choose user name',
            ])->add();
        $this->form('inventory_id', 'Inventory Name', 'select')
            ->options([
                'data' => DB::table('inventories')->get(),
                'name' => 'name',
                'value' => 'id'
            ])
            ->validation([
                'required' => 'Choose Product',
            ])->add();
        $this->form('price', 'Price', 'number')->add();
        $this->form('stocks', 'Stock', 'number')->add();
    }

    public function editQuery(&$query)
    {
        $query->leftJoin('users', 'users.id', '=', 'inventories_users.user_id')
            ->select('inventories_users.*', 'users.name as user_name');
        $query->leftJoin('inventories', $this->tableName.".inventory_id", '=', 'inventories.id')
            ->addSelect($this->tableName.'.id',
                        'inventory_id',
                        'stocks',
                        'price',
                        DB::raw('inventories.name as inventory_name'));
    }
}
