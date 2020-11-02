<?php
namespace Energibangsa\Cepet\Traits;

use Energibangsa\Cepet\EB;
use Request;
use DataTables;
use DB;

/**
 * 
 */
trait BreadTrait {

    protected $dataTable;
    protected $queryTable;
    protected $col;
    protected $cols;
    protected $table;
    protected $form;
    protected $forms;
    protected $trashBtn = false;
    protected $backBtn = false;

    protected $postdata;
    protected $paramUrl;
    protected $sequence;

    protected $scriptAddPage;
    protected $scriptEditPage;
    protected $scriptClosePage;

    protected $filters;
    protected $filter;
    protected $trash = false;

    protected function filter($name, $label, $type, $diff = array(), $params = array())
    {
        $this->filter = [];
        $this->filter = [
            'name'       => $name,
            'label'      => $label,
            'type'       => $type,
            'diff'       => $diff,
            'params'     => $params,
        ];
        
        return $this;
    }

    protected function scriptAddPage($script)
    {
        $this->scriptAddPage = $script;
        return $this;
    }

    protected function scriptEditPage($script)
    {
        $this->scriptEditPage = $script;
        return $this;
    }

    protected function scriptClosePage($script)
    {
        $this->scriptClosePage = $script;
        return $this;
    }

    private function actionBtn($status = true)
    {
        return $this->actionBtn = $status;
    }

    protected function trashBtn($status = true)
    {
        return $this->trashBtn = $status;
    }

    protected function addBtn($status = true)
    {
        return $this->addBtn = $status;
    }

    protected function column($name, $label, $data = null)
    {
        $this->col = [];
        $this->col = [
            'name' => $name,
            'label' => $label,
            'data' => $data ?? (strpos($name, '.') ? explode('.', $name)[1] : $name),
        ];
        return $this;
    }

    // additional condition dor datatable column
    protected function additional($additional = array())
    {
        $this->col['additional'] = $additional;
        return $this;
    }

    // inititate form
    protected function form($name, $label, $input)
    {
        $this->form = [];
        $this->form = [
            'name'       => $name,
            'label'      => $label,
            'input'       => $input,
        ];
        return $this;
    }

    // placeholder
    protected function placeholder($placeholder)
    {
        $this->form['placeholder'] = $placeholder;
        return $this;
    }

    // Jquery validation rules for form
    protected function validation($rules = array())
    {
        $this->form['rules'] = $rules;
        return $this;
    }

    // option data for form
    protected function options($params = array())
    {
        $options = [];
        if(gettype($params['data']) == "array") {
            $options = $params['data'];
        } else {
            foreach ($params['data'] as $data) {
                $options[$data->{$params['value']}] = $data->{$params['name']};
            }
        }
        if (isset($params['empty'])) {
            $options[''] = '-- Tidak Ada --';
        }

        $this->form['options'] = $options;
        return $this;
    }

    // parent_id
    protected function parent($id_parent, $table, $id_child, $name_child)
    {
        $this->form['parent'] = [
            'id_parent' => $id_parent,
            'table' => $table,
            'id_child' => $id_child,
            'name_child' => $name_child,
        ];

        return $this;
    }

    public function getChild()
    {
        $request = Request::input();
        $data = DB::table($request['table'])->where($request['id_child'], $request['id'])->get();
        return response()->json($data);
    }
    
    protected function editDisabled()
    {
        $this->form['editDisabled'] = "edit-disabled";
        return $this;
    }

    // add atribute for form
    protected function attribute($str = "")
    {
        $this->form['attr'] = $str;
        return $this;
    }

    // add to array form and col
    protected function add()
    {
        if (!empty($this->form)) {
            $this->forms[] = $this->form;
            $this->form    = [];
        } elseif(!empty($this->filter)) {
            $this->filters[] = $this->filter;
            $this->filter    = [];
        } else {
            $this->cols[] = $this->col;
            $this->col    = [];
        }

        return $this;
    }
}