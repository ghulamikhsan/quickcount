<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

class CategoriesController extends BaseController
{
    public function init()
    {
        $this->title        = 'Category';
        $this->tableName    = 'categories';
        $this->pk           = 'id';

        $this->trashBtn();

        $this->column('id', 'ID')->add();
        $this->column('name', 'Name')->add();

        $this->form('name', 'Name', 'text')
            ->validation([
                'required' => 'Input Category Name!',
            ])->add();
    }
}
