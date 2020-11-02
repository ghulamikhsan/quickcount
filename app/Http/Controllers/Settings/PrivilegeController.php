<?php

namespace App\Http\Controllers\settings;

use Energibangsa\Cepet\controllers\BaseController;


class PrivilegeController extends BaseController
{
    public function init()
    {
        $this->title     = 'Privileges';
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
            ])
            ->editDisabled()
            ->add();
    }
}
