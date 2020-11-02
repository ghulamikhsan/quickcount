<?php

namespace App\Http\Controllers;

use Request;
use Energibangsa\Cepet\controllers\BaseController;
use DB;
use Validator;

class HomeController extends BaseController
{
    public function init()
    {
        $this->title     = 'Home';
        $this->tableName = 'privileges';
        $this->pk        = 'id';

        $this->trashBtn();
        
        // Initiate Column
        $this->column('id', 'ID')->add();
        $this->column('name', 'Nama')->add();
        
        // initiate form
        $this->form('name', 'Nama', 'text')
            ->validation([
                'required' => 'Harap isi Nama',
            ])->add();
        $this->form('test', 'Test', 'test')->add();
    }

    public function getTest()
    {
        $date = new \DateTime();
        $timeZone = $date->getTimezone();
        echo date_default_timezone_get();

    }

    public function getReset()
    {
        $tables = request('table');

        if (count($tables) == 0) {
            return "Tidak ada table";
        }

        $trtb = "";
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $trtb .= $table." ";
        }

        return "Sukses Mengosongkan tabel ".$trtb;
    }
}
