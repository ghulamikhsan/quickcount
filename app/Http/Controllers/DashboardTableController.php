<?php

namespace App\Http\Controllers;

use Energibangsa\Cepet\controllers\BaseController;

use DB;
use Hash;
use Request;
use Auth;

class DashboardTableController extends BaseController
{
    public function init()
    {
        $this->title     = 'Detail';
        $this->tableName = 'poling_details';
        $this->pk        = 'id';

       
        
        $this->column('id', 'ID')->add();
        $this->column('polings.name as name', 'Nama TPS', 'poling_id')->add();
        $this->column('calons.name', 'Nama Calon', 'calon_id')->add();
        $this->column('count', 'Total Suara')->add();

        $this->form('poling_id', 'Nama TPS', 'select')
            ->options([
                'data' => DB::table('polings')->get(),
                'value' => 'id',
                'name' => 'tps_name',
            ])
            ->validation([
                'required' => 'Harap isi Nama TPS',
            ]);
       
            
    }

    public function editQuery(&$query)
    {
        $query
                ->join('polings', $this->tableName.".poling_id", '=', 'polings.id')
                ->join('calons', $this->tableName.".calon_id", '=', 'calons.id')
                ->select($this->tableName.'.id',
                        'poling_id',
                        'calons.name as calon_id',
                        'count',
                        'poling_details.created_at as waktu',
                        'polings.tps_name as poling_id');
    }
}
