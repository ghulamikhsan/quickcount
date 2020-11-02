<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

class SubscriptionsController extends BaseController
{
    public function init()
    {
        $this->title        = 'Subscriptions';
        $this->tableName    = 'subscriptions';
        $this->pk           = 'id';

        $this->trashBtn();

        $this->column('id', 'ID')->add();
        $this->column('name', 'Nama')->add();
        $this->column('month', 'Jangka Waktu (Bulan)')->add();

        $this->form('name', 'Nama', 'text')
            ->validation([
                'required' => 'Input Category Name!',
            ])->add();
        $this->form('month', 'Jangka Waktu (Bulan)', 'number')
            ->validation([
                'required' => 'Masukkan jangka waktu',
            ])->add();
    }
}
