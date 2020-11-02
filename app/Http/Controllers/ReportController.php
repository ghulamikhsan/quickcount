<?php

namespace App\Http\Controllers;

use Energibangsa\Cepet\controllers\BaseController;

use DB;
use Hash;
use Request;
use Auth;

class ReportController extends BaseController
{
    public function init()
    {
        $this->title     = 'Detail';
        $this->tableName = 'poling_details';
        $this->pk        = 'id';

        // $this->trashBtn();
        
        // Initiate Column
        $this->column('id', 'ID')->add();
        $this->column('polings.name as name', 'Nama TPS', 'poling_id')->add();
        $this->column('calons.name', 'Nama Calon', 'calon_id')->add();
        $this->column('count', 'Total Suara')->add();
        // $this->column('created_at', 'Waktu')->add();
    
        // $this->form('tps_name' , 'Nama TPS', 'text')
        //     ->validation([
        //         'required' => 'Harap isi Nama TPS',
        //     ])
        //     ->add();

        $this->form('poling_id', 'Nama TPS', 'select')
            ->options([
                'data' => DB::table('polings')->get(),
                'value' => 'id',
                'name' => 'tps_name',
            ])
            ->validation([
                'required' => 'Harap isi Nama TPS',
            ])
            ->add();

        $this->form('calon_id', 'Nama Calon', 'select')
            ->options([
                'data' => DB::table('calons')->get(),
                'value' => 'id',
                'name' => 'name',
            ])
            ->validation([
                'required' => 'Harap isi Nama Calon',
            ])
            ->add();

         $this->form('count' , 'Jumlah Suara', 'number')
            ->validation([
                'required' => 'Harap isi Jumlah Suara ',
            ])
            ->add();
            
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
