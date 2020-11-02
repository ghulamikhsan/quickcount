<?php

namespace App\Http\Controllers;

use Energibangsa\Cepet\controllers\BaseController;

use DB;
use Hash;
use Request;

class CalonController extends BaseController
{
    public function init()
    {
        $this->title     = 'Calon';
        $this->tableName = 'calons';
        $this->pk        = 'id';

        $this->trashBtn();
        
        // Initiate Column
        $this->column('id', 'ID')->add();
        $this->column('calons.name', 'Nama')->add();
        $this->column('number', 'Nomor Urut')->add();
        $this->column('created_at', 'Waktu')->add();

        $this->form('name' , 'Nama', 'text')
            ->validation([
                'required' => 'Harap isi Nama Calon',
            ])
            ->add();

        $this->form('number', 'Nomor Urut', 'number')
           ->validation([
                'required' => 'unique|Harap isi Nomor Urut Calon',
            ])
            ->add();
        }

}
