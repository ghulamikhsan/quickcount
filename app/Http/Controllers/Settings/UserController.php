<?php

namespace App\Http\Controllers\settings;

use Energibangsa\Cepet\controllers\BaseController;

use DB;
use Hash;
use Request;

class UserController extends BaseController
{
    public function init()
    {
        $this->title     = 'Users';
        $this->tableName = 'users';
        $this->pk        = 'id';

        $this->trashBtn();
        
        // Initiate Column
        $this->column('id', 'ID')->add();
        $this->column('users.name', 'Nama')->add();
        $this->column('users.username', 'Username')->add();
        $this->column('phone', 'No HP')->add();
        $this->column('created_at', 'Waktu')->add();
        
        // initiate form
        $this->form('privilege_id', 'Privilege', 'select')
            ->options([
                'data' => DB::table('privileges')->get(),
                'value' => 'id',
                'name' => 'name'
            ])
            ->validation([
                'required' => 'Harap isi Privilege',
            ])
            ->editDisabled()
            ->add();
        $this->form('name' , 'Nama', 'text')
            ->validation([
                'required' => 'Harap isi Nama',
            ])
            ->add();
        $this->form('username' , 'Username', 'username')
            ->validation([
                'required' => 'Harap isi Username',
                // 'username' => 'Harus berupa Username',
                'unique:users,username'.(Request::input('id') ? ','.Request::input('id') : '' ) => 'Username Sudah Terdaftar'
            ])
            ->add();
            
        $this->form('password' , 'Password', 'password');
        $this->section == 'add' && $this->validation([
            'required' => 'Harap isi Password',
         ]);
        $this->add();

        $this->form('phone', 'Nomor HP', 'number')
            ->validation([
                'unique:users,phone'.(Request::input('id') ? ','.Request::input('id') : '' ) => 'No HP Sudah Terdaftar'
            ])
            ->add();

    }
    
    public function editQuery(&$query) {
        $prefix = DB::getTablePrefix();

        $query->leftJoin('privileges', $this->tableName.".privilege_id", '=', 'privileges.id')
            ->select($this->tableName.'.*', 
                        'privilege_id', 
                        DB::raw($prefix.'users.name as name'), 
        //                 DB::raw('DATE_FORMAT(expired, "%Y-%m-%d") as expired'),
                        DB::raw($prefix.'privileges.name as privilege_name'));
    }

    public function beforeAdd(&$postdata)
    {
        $postdata['password'] = Hash::make($postdata['password']);
    }

    public function beforeEdit(&$postdata, &$id)
    {
        if(empty($postdata['password'])) {
            unset($postdata['password']);
        } else {
            $postdata['password'] = Hash::make($postdata['password']);
        } 
    }
}
