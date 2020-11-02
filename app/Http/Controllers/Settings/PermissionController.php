<?php

namespace App\Http\Controllers\Settings;

use Energibangsa\Cepet\controllers\BaseController;
use DB;


class PermissionController extends BaseController
{
    public function init()
    {
        $this->title     = 'Permissions';
        $this->tableName = 'permissions';
        $this->pk        = 'id';

        // Initiate Column
        $this->column('id', 'ID')->add();
        $this->column('privileges.name', 'Privilege')->add();
        $this->column('menus.title', 'Menu')->add();
        $this->column('browse', 'Browse')->add();
        $this->column('read', 'Read')->add();
        $this->column('edit', 'Edit')->add();
        $this->column('add', 'Add')->add();
        $this->column('delete', 'Delete')->add();
        $this->column('trash', 'Trash')->add();
        
        // initiate form
        $this->form('menu_id', 'Menu', 'select')
        ->options([
            'data' => DB::table('menus')->get(),
            'value' => 'id',
            'name' => 'title'
        ])
        ->add();
        $this->form('privilege_id', 'Privilege', 'select')
            ->options([
                'data' => DB::table('privileges')->get(),
                'value' => 'id',
                'name' => 'name'
            ])
            ->validation([
                'required' => 'Please select your privilege!'
            ])->add();
        $this->form('browse', 'Browse', 'select')
        ->options([
            'data' => ['Tidak', 'Ya'],
        ])
        ->add();
        $this->form('read', 'Read', 'select')
        ->options([
            'data' => ['Tidak', 'Ya'],
        ])
        ->add();
        $this->form('edit', 'Edit', 'select')
        ->options([
            'data' => ['Tidak', 'Ya'],
        ])
        ->add();
        $this->form('add', 'Add', 'select')
        ->options([
            'data' => ['Tidak', 'Ya'],
        ])
        ->add();
        $this->form('delete', 'Delete', 'select')
        ->options([
            'data' => ['Tidak', 'Ya'],
        ])
        ->add();
        $this->form('trash', 'Trash', 'select')
        ->options([
            'data' => ['Tidak', 'Ya'],
        ])
        ->add();
    }

    public function editQuery(&$query)
    {
        $query->leftJoin('menus', 'menus.id', '=', 'permissions.menu_id')
            ->select('permissions.*', 'menus.title');
        $query->leftJoin('privileges', $this->tableName.".privilege_id", '=', 'privileges.id')
        ->addSelect($this->tableName.'.id',
        'privilege_id',
        'browse',
        'read',
        'add',
        'edit',
        'delete',
        'trash',
        'privileges.name');
    }
}
