<?php  

namespace Energibangsa\Cepet\controllers;

use App\Http\Controllers\Controller;
use Energibangsa\Cepet\traits\BreadTrait;
use Energibangsa\Cepet\helpers\EB;

use DataTables;
use Request;
use DB;
use Validator;
use Schema;


/**
 * 
 * RESPONSE CATATAN
 * status (0: gagal, 1: success)
 * code:status=0 (101: input error, 102: jwt error, 103: cek error, 104: not found)
 * message: message
 * 
 */
class BaseController extends Controller
{
    use BreadTrait;

    public $title;
    public $pk;
    public $tableName;
    public $validator = [];
    public $valid     = true;
    public $response   = null;
    public $actionBtn = true;
    public $addBtn = true;
    public $section = 'index';
    public $rawColumns = [];

    public function init() {}

    public function getIndex()
    {
        $this->section = 'index';
        $this->init();
        
        if ($this->actionBtn) {
            $this->column('actions', '#')
                ->additional([
                    'width' => 100,
                    'orderable' => 'false',
                ])->add();
        }

        $data['title']           = $this->title;
        $data['cols']            = $this->cols;
        $data['forms']           = $this->forms;
        $data['filters']         = $this->filters;
        $data['addBtn']          = $this->addBtn;
        $data['actionBtn']       = $this->actionBtn;
        $data['trashBtn']        = $this->trashBtn;
        $data['backBtn']         = $this->backBtn;
        $data['trash']           = $this->trash;
        $data['scriptAddPage']   = $this->scriptAddPage;
        $data['scriptEditPage']  = $this->scriptEditPage;
        $data['scriptClosePage'] = $this->scriptClosePage;
        
        return view('views::layouts.master', $data);
    }

    public function getData()
    {
        if (Request::ajax()) {
            $this->init();
            $this->table = DB::table($this->tableName);

            $id = Request::input('id');
            if (!empty($id)) {

                #GET DATA
                $data = $this->table->where($this->pk, $id);

                #CEK DATA FOUND
                if($data->count() == 0){
                    $res['status']  = 0;
                    $res['message'] = 'Data Not Found';
                    return response()->json($res);
                }

                #SUKSES
                $res['status']  = 1;
                $res['message'] = 'success';
                $res['data']        = $data->first();
                return response()->json($res);
            }

            if (Request::input('trash')) {
                $this->table = $this->table->whereNotNull($this->tableName.'.deleted_at');
            } else {
                if (Schema::hasColumn($this->tableName, 'deleted_at')) {
                    $this->table = $this->table->whereNull($this->tableName.'.deleted_at');
                }
            }
            
            $this->editQuery($this->table);
            $this->dataTable = DataTables::of($this->table);
            $this->editDataTable($this->dataTable);
    
            // add action
                if(Request::input('trash')) {
                    $this->dataTable->addColumn('actions', function($data) {
                        $pk = $this->pk;
                        return '
                            <div class="btn-group">
                                    <button type="button" class="btn btn-warning">Aksi</button>
                                    <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="" onclick="javascript:doPermanentDelete('.$data->$pk.');">Hapus Permanen</a>
                                    <a class="dropdown-item" href="" onclick="javascript:doRestore('.$data->$pk.')">Kembalikan</a>
                                </div>
                            </div>';
                    });
                } else {
                    if ($this->actionBtn) {
                        $this->dataTable->addColumn('actions', function($data) {
                            $pk = $this->pk;
                            return '<div class="btn-group">
                                        <button type="button" class="btn btn-warning">Aksi</button>
                                        <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="" onclick="javascript:openForm('.$data->$pk.');">Edit</a>
                                        <a class="dropdown-item" href="" onclick="javascript:doDelete('.$data->$pk.')">Hapus</a>
                                    </div>
                                </div>';
                        });
                    }
                }
                $this->dataTable->rawColumns(array_merge(['actions'], $this->rawColumns));
            return $this->dataTable->make(true);
        }

    }

    // Edit Query
    public function editQuery(&$query) {}
    public function editDataTable(&$dataTable) {}

    public function postAdd()
    {
        $this->section = 'add';

        $this->init();

        // set postdata
        $this->postdata = Request::input();
        if(isset($this->postdata['_token'])) unset($this->postdata['_token']);

        $validator = $this->setValidator();
        $this->validator($validator['rules'], $validator['messages']);

        DB::beginTransaction();
        try {
            $this->beforeAdd($this->postdata);

            // check uploaded file
            foreach ($this->forms as $form) {
                if ($form['input'] == 'file') {
                    if (Request::file($form['name'])) {
                        $upload = EB::uploadFile($form['name'], 'public/'.request()->segment(2), null, ['allowed_type' => 'jpeg|png|jpg']);
                        if (!$upload['status']) {
                            throw new \Exception($upload['message']);
                        } else {
                            $this->postdata[$form['name']] = $upload['filename'];
                        }
                    } else {
                        unset($this->postdata[$form['name']]);
                    }
                }
            }

            $result = EB::insertID($this->tableName, $this->postdata);

            if($result){
                $this->afterAdd($this->postdata, $result);
                DB::commit();
                $res['status']  = 1;
                $res['message'] = 'Data berhasil ditambahkan.';
            }else{
                DB::rollBack();
                $res['status']  = 0;
                $res['message'] = 'Mengalami masalah.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $res['status']  = 0;
            $res['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $res['status']  = 0;
            $res['message'] = $e->getMessage();
        }

        return response()->json($res);
    }

    public function beforeAdd(&$postdata) {}
    public function afterAdd(&$postdata, &$id) {}

    public function postEdit()
    {
        $this->section = 'edit';

        $this->init();

        // set postdata
        $this->postdata = Request::input();
        if(isset($this->postdata['_token'])) unset($this->postdata['_token']);

        $validator = $this->setValidator(true);
        $this->validator($validator['rules'], $validator['messages']);

        $id = $this->postdata['id'];
        unset($this->postdata['id']);
        $this->beforeEdit($this->postdata, $id);
        
        $where = array([$this->pk,'=', $id]);
        try {
            foreach ($this->forms as $form) {
                if ($form['input'] == 'file') {
                    if (Request::file($form['name'])) {
                        $upload = EB::uploadFile($form['name'], 'public/'.request()->segment(2), null, ['allowed_type' => 'jpeg|jpg|png']);
                        if (!$upload['status']) {
                            throw new \Exception($upload['message']);
                        } else {
                            $this->postdata[$form['name']] = $upload['filename'];
                        }
                    } else {
                        unset($this->postdata[$form['name']]);
                    }
                }
            }
            $result = EB::update($this->tableName, $this->postdata, $where);
            if($result){
                $this->afterEdit($this->postdata, $id);
                DB::commit();
                $res['status']  = 1;
                $res['message'] = 'Data berhasil diubah.';
            }else{
                DB::rollBack();
                $res['status']  = 0;
                $res['message'] = 'Mengalami masalah.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $res['status']  = 0;
            $res['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $res['status']  = 0;
            $res['message'] = $e->getMessage();
        }

        return response()->json($res);
    }

    public function beforeEdit(&$postdata, &$id) {}
    public function afterEdit(&$postdata, &$id) {}

    public function postDelete()
    {
        $this->init();

        $this->postdata = Request::input();
        if(isset($this->postdata['_token'])) unset($this->postdata['_token']);
        DB::beginTransaction();

        try {
            $this->beforeDelete($this->postdata['id']);
            $delete = DB::table($this->tableName)->where($this->pk, '=', $this->postdata["id"]);
            $deleted_data = $delete->first();
            $result = EB::delete($this->tableName, $delete);

            foreach ($this->forms as $form) {
                if ($form['input'] == 'file') {
                    if ($delete->count() == 0) {
                        \File::delete(storage_path('app/' . $deleted_data->{$form['name']}));
                    }
                }
            }
            
            if($result){
                $this->afterDelete($this->postdata["id"]);
                DB::commit();
                $res['status']  = 1;
                $res['message'] = 'Data berhasil dihapus.';
            }else{
                DB::rollBack();
                $res['status']  = 0;
                $res['message'] = 'Mengalami masalah.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $res['status']  = 0;
            $res['message'] = $e->getMessage();
        }
        
        return response()->json($res);
    }

    public function beforeDelete(&$id) {}
    public function afterDelete(&$id) {}

    public function postPermanentDelete($id)
    {
        $this->init();
        $this->postdata = Request::input();
        if(isset($this->postdata['_token'])) unset($this->postdata['_token']);

        $id = $this->postdata['id'];
        unset($this->postdata['id']);
        
        DB::beginTransaction();
        try {
            $this->beforePermanentDelete($id);
            $delete = DB::table($this->tableName)->where($this->pk, '=', $id);
            $deleted_data = $delete->first();
            $result = EB::permanentDelete($this->tableName, $delete);
            
            foreach ($this->forms as $form) {
                if ($form['input'] == 'file') {
                    if ($delete->count() == 0) {
                        \File::delete(storage_path('app/' . $deleted_data->{$form['name']}));
                    }
                }
            }

            if($result){
                $this->afterPermanentDelete($id);
                DB::commit();
                $res['status']  = 1;
                $res['message'] = 'Data berhasil dihapus.';
            }else{
                DB::rollBack();
                $res['status']  = 0;
                $res['message'] = 'Mengalami masalah.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $res['status']  = 0;
            $res['message'] = 'DB Error: Query Error.';
        }
        
        return response()->json($res);   
    }

    public function beforePermanentDelete(&$id) {}
    public function afterPermanentDelete(&$id) {}

    public function postRestore($id)
    {
        $this->init();
        $this->postdata = Request::input();
        if(isset($this->postdata['_token'])) unset($this->postdata['_token']);

        $id = $this->postdata['id'];
        unset($this->postdata['id']);
        $where = array([$this->pk,'=', $id]);
        
        DB::beginTransaction();
        try {
            $this->beforeRestore($id);
            $result = EB::restore($this->tableName, $where);

            if($result){
                $this->afterRestore($id);
                DB::commit();
                $res['status']  = 1;
                $res['message'] = 'Data berhasil dikembalikan.';
            }else{
                DB::rollBack();
                $res['status']  = 0;
                $res['message'] = 'Mengalami masalah.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $res['status']  = 0;
            $res['message'] = $e->getMessage();
        }

        return response()->json($res);
    }

    public function beforeRestore(&$id) {}
    public function afterRestore(&$id) {}
    
    public function input($name, $rule = null, $message = null){
        $rule && $this->validator[$name] = $rule;
        $rule && $this->validator[$name] = $rule;
        return Request::input($name);
    }

    public function setValidator($edit = false) {
        $rules = [];
        $messages = [];
        
        foreach ($this->forms as $form) {
            if ( $edit && isset($form['editDisabled'])) continue;
            if (isset($form['rules'])) {
                $rules[$form['name']] = '';
                    foreach ($form['rules'] as $key => $value) {
                        $rules[$form['name']] .= $key.'|'; 
                        preg_match('/^[A-z]+/', $key, $msg_name);
                        $messages[$form['name'].'.'.$msg_name[0]] = $value;
                    }
    
                $rules[$form['name']] = rtrim($rules[$form['name']],'|');    
            }
            
        }

        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }

    public function validator($rules, $messages = null)
    {
        if(empty($rules) && empty($messages)) return;

        $validator = Validator::make(Request::all(), $rules, $messages);
        if ($validator->fails()) {
            $result = array();
            $message = $validator->errors();
            
            EB::createLog($message, 'VALIDATOR', 'warning');

            $result['status']  = 0;
            $result['code']    = 101;
            $result['message'] = 'Mengalami Masalah data yang di Input';
            $result['errors']  = $message;

            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    // $message = [
    //     'input_name' => ['Error Message']
    // ]
    public function setErrorForm($message)
    {
        $result['status']  = 0;
        $result['code']    = 101;
        $result['message'] = 'Mengalami Masalah data yang di Input';
        $result['errors']  = $message;

        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    public function output($response)
    {
        return response()->json($response);
    }
}