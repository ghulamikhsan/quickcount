<?php

namespace App\Http\Controllers\Settings;

use Energibangsa\Cepet\controllers\BaseController;

use DB;

class MenuController extends BaseController
{
    public function init()
    {
        $this->title      = 'Menu';
        $this->tableName  = 'menus';
        $this->pk         = 'id';

        $this->column('id', 'ID')->add();
        $this->column('parent', 'Parent')->add();
        $this->column('title', 'Title')->add();
        $this->column('orderable', 'Orderable')->add();
        $this->column('icon', 'Icon')->add();
        $this->column('page', 'Page')->add();
        $this->column('new_tab', 'New Tab')->add();
        $this->column('section', 'Section')->add();

        $this->form('title', 'Title', 'text')
            ->validation([
                'required' => 'Input your title!',
            ])->add();
        $this->form('up_id', 'Parent', 'select')
            ->options([
                'data' => (object)array_merge([(object)[
                    'id' => '',
                    'title' => 'Select Parent'
                ]], DB::table('menus')->where('up_id', null)->get()->toArray()),
                'value' => 'id',
                'name' => 'title'
            ])
            ->add();
        $this->form('orderable', 'Orderable', 'number')
            ->validation([
                'required' => 'Input your Orderable!',
            ])->add();
        $this->form('icon', 'Icon', 'text')->add();
        $this->form('page', 'Page', 'text')->add();
        $this->form('new_tab', 'New Tab', 'switch')->add();
        $this->form('section', 'Section', 'switch')->add();
    }

    public function editQuery(&$query)
    {
        $query->leftJoin('menus as mn', 'menus.up_id', '=', 'mn.id')->select('menus.*', 'mn.title as parent');
    }

    public function editDataTable(&$dataTable)
    {
        $dataTable->editColumn('new_tab', function($data) {
            return $data->new_tab == 1 ? 'Yes' : 'No';
        });
        $dataTable->editColumn('section', function($data) {
            return $data->section == 1 ? 'Yes' : 'No';
        });
    }
}