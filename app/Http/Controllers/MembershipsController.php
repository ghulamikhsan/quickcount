<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseController;
use Auth;
use DB;

class MembershipsController extends BaseController
{
    public function init()
    {
        $this->title        = 'Berlangganan';
        $this->tableName    = 'users_subscriptions';
        $this->pk           = 'stockist_code';
        $this->actionBtn    = true;

        $this->column('stockist_code', 'Kode Stockist')->add();
        $this->column('membership', 'Membership')->add();

        $this->form('stockist_code', 'Kode Stockist', 'select')
            ->options([
                'data' => DB::table('users')->where('privilege_id', 2)->get(),
                'value' => 'code',
                'name' => 'code',
            ])
            ->validation([
                'required' => 'Kode Stockist wajib diisi',
            ])->add();
        $this->form('membership', 'Membership', 'date')
            ->validation([
                'required' => 'Membership wajib diisi'
            ])->add();
    }

    public function editQuery(&$query) {
        $query->orderBy('membership', 'asc');
    }
    
    public function beforeAdd(&$postdata) {
        $data = DB::table($this->tableName)->where('stockist_code', $postdata['stockist_code']);

        if ($data->count() > 0) {
            $data->delete();
        }
    }

    public function editDataTable(&$dataTable) {
        $dataTable->editColumn('membership', function($row) {
            $date=date_create($row->membership);
            return date_format($date,"d/m/Y");
        });
    }
}
