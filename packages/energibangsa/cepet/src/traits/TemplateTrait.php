<?php
namespace Energibangsa\Cepet\Traits;

use DataTables;
use DB;
use Request;
use Schema;
use Energibangsa\Cepet\EB;

/**
 * 
 */
trait TemplateTrait
{
    protected $_title;
    protected $_tableName;
    protected $_dataTable;
    protected $_queryTable;
    protected $_col;
    protected $_cols;
    protected $_table;
    protected $_pk;
    protected $_form;
    protected $_forms;
    protected $_actionBtn = true;
    protected $_trashBtn = false;
    protected $_addBtn = true;
    protected $_backBtn = false;

    protected $_postdata;
    protected $_paramUrl;
    protected $_sequence;

    protected $_scriptAddPage;
    protected $_scriptEditPage;
    protected $_scriptClosePage;

    protected $_filters;
    protected $_filter;
    protected $_trash = false;

    //  inititate Get
    // protected function init($title, $table, $pk = "id")
    // {
    //     if($this->_filters) {
    //         foreach ($this->_filters as $filter) {
    //             if(Request::input('filter_'.$filter['name']))
    //                 $table->where($table->from.'.'.$filter['name'],
    //                         Request::input('diff_'.$filter['name']), 
    //                         Request::input('filter_'.$filter['name']));
    //         }
    //     }
    //     $this->_title     = $title;
    //     $this->_pk        = $pk;
    //     $this->_table     = $table;
    //     $this->_dataTable = DataTables::of($this->_table);

    //     // add action
    //     if(Request::input('trash')) {
    //         $this->_dataTable->addColumn('actions', function($data) {
    //             $pk = $this->_pk;
    //             return '<div class="btn-group mr-2" role="group" aria-label="...">
    //                 <button onclick="doRestore('.$data->$pk.')" type="button" class="m-btn btn btn-secondary">
    //                     <i class="la la-rotate-left"></i>
    //                 </button>
    //                 <button onclick="doPermanentDelete('.$data->$pk.')" class="m-btn btn btn-secondary">
    //                     <i class="la la-trash"></i>
    //                 </button>
    //             </div>';
    //         });
    //     } else {
    //         $this->_dataTable->addColumn('actions', function($data) {
    //             $pk = $this->_pk;
    //             return '<div class="btn-group mr-2" role="group" aria-label="...">
    //                 <button onclick="pageForm(true, '.$data->$pk.')" type="button" class="m-btn btn btn-secondary">
    //                     <i class="la la-pencil"></i>
    //                 </button>
    //                 <button onclick="doDelete('.$data->$pk.')" class="m-btn btn btn-secondary">
    //                     <i class="la la-trash"></i>
    //                 </button>
    //             </div>';
    //         });
    //     }
    //     $this->_dataTable->rawColumns(['actions']);

    //     $this->column('actions', '#')
    //         ->additional([
    //             'width' => 100,
    //             'orderable' => 'false',
    //         ])->add();

    //     return $this;
    // }

    // inititate POST
    // protected function initPost($tableName, $paramUrl, $params = array())
    // {
    //     $this->_postdata  = Request::input();
    //     $this->_tableName = $tableName;
    //     $this->_paramUrl  = $paramUrl;
    //     $this->_pk        = $params['pk'] ?? "id";
    //     $this->_sequence  = $params['sequence'] ?? "";

    //     if(isset($this->_postdata['_token'])) unset($this->_postdata['_token']);
    //     return $this;
    // }

    // Post Validation
    // protected function postValidation($url, $func)
    // {
    //     if ($this->_paramUrl == $url) {
    //         $validation = call_user_func($func, $this->_postdata);
    //         if($validation) {
    //             $validation->send();
    //             exit();
    //         }
    //     }

    //     return $this;
    // }

    // Simpan config
    // protected function save($func = "")
    // {
    //     DB::beginTransaction();
    //     switch ($this->_paramUrl) {
    //         case 'delete':
    //             try {
    //                 $delete = DB::table($this->_tableName)->where($this->_pk, '=', $this->_postdata["id"]);
    //                 $result = EB::delete($this->_tableName, $delete);
        
    //                 if($result){
    //                     $func != "" && call_user_func($func, $this->_postdata);
    //                     DB::commit();
    //                     $res['api_status']  = 1;
    //                     $res['api_message'] = 'Data berhasil dihapus.';
    //                 }else{
    //                     DB::rollBack();
    //                     $res['api_status']  = 0;
    //                     $res['api_message'] = 'Mengalami masalah.';
    //                 }
    //             } catch (\Illuminate\Database\QueryException $e) {
    //                 DB::rollBack();
    //                 $res['api_status']  = 0;
    //                 $res['api_message'] = 'DB Error: Query Error.';
    //             }
                
    //             return response()->json($res);

    //             break;
    //         case'permanent-delete':
    //             try {
    //                 $delete = DB::table($this->_tableName)->where($this->_pk, '=', $this->_postdata["id"]);
    //                 $result = EB::permanentDelete($this->_tableName, $delete);
        
    //                 if($result){
    //                     $func != "" && call_user_func($func, $this->_postdata);
    //                     DB::commit();
    //                     $res['api_status']  = 1;
    //                     $res['api_message'] = 'Data berhasil dihapus.';
    //                 }else{
    //                     DB::rollBack();
    //                     $res['api_status']  = 0;
    //                     $res['api_message'] = 'Mengalami masalah.';
    //                 }
    //             } catch (\Illuminate\Database\QueryException $e) {
    //                 DB::rollBack();
    //                 $res['api_status']  = 0;
    //                 $res['api_message'] = 'DB Error: Query Error.';
    //             }
                
    //             return response()->json($res);    
    //             break;
    //         case'restore':
    //             $id = $this->_postdata['id'];
    //             $where = array([$this->_pk,'=', $id]);
    //             try {
    //                 $result = EB::restore($this->_tableName, $where);

    //                 if($result){
    //                     $func != "" && call_user_func($func, $this->_postdata);
    //                     DB::commit();
    //                     $res['api_status']  = 1;
    //                     $res['api_message'] = 'Data berhasil dikembalikan.';
    //                 }else{
    //                     DB::rollBack();
    //                     $res['api_status']  = 0;
    //                     $res['api_message'] = 'Mengalami masalah.';
    //                 }
    //             } catch (\Illuminate\Database\QueryException $e) {
    //                 DB::rollBack();
    //                 $res['api_status']  = 0;
    //                 $res['api_message'] = 'DB Error: Query Error.';
    //             }

    //             return response()->json($res);
    //             break;
    //         case 'edit':
    //             $id = $this->_postdata['id'];
    //             unset($this->_postdata['id']);
    //             $where = array([$this->_pk,'=', $id]);
    //             try {
    //                 $result = EB::update($this->_tableName, $this->_postdata, $where);

    //                 if($result){
    //                     $func != "" && call_user_func($func, $this->_postdata);
    //                     DB::commit();
    //                     $res['api_status']  = 1;
    //                     $res['api_message'] = 'Data berhasil diubah.';
    //                 }else{
    //                     DB::rollBack();
    //                     $res['api_status']  = 0;
    //                     $res['api_message'] = 'Mengalami masalah.';
    //                 }
    //             } catch (\Illuminate\Database\QueryException $e) {
    //                 DB::rollBack();
    //                 $res['api_status']  = 0;
    //                 $res['api_message'] = 'DB Error: Query Error.';
    //             }

    //             return response()->json($res);
    //             break;
    //         default:
    //             $this->_postdata[$this->_pk] = EB::seq($this->_sequence);
    //             try {
    //                 $result = EB::insert($this->_tableName, $this->_postdata);

    //                 if($result){
    //                     $func != "" && call_user_func($func, $this->_postdata);
    //                     DB::commit();
    //                     $res['api_status']  = 1;
    //                     $res['api_message'] = 'Data berhasil ditambahkan.';
    //                 }else{
    //                     DB::rollBack();
    //                     $res['api_status']  = 0;
    //                     $res['api_message'] = 'Mengalami masalah.';
    //                 }
    //             } catch (\Illuminate\Database\QueryException $e) {
    //                 DB::rollBack();
    //                 $res['api_status']  = 0;
    //                 $res['api_message'] = 'DB Error: Query Error.';
    //             }

    //             return response()->json($res);
    //             break;
    //     }
    // }

    // protected function editDataTable($func)
    // {
    //     $this->_dataTable = call_user_func($func, $this->_dataTable);
    //     return $this;
    // }

    // protected function scriptAddPage($script)
    // {
    //     $this->_scriptAddPage = $script;
    //     return $this;
    // }

    // protected function scriptEditPage($script)
    // {
    //     $this->_scriptEditPage = $script;
    //     return $this;
    // }

    // protected function scriptClosePage($script)
    // {
    //     $this->_scriptClosePage = $script;
    //     return $this;
    // }

    // private function actionBtn($status = true)
    // {
    //     return $this->_actionBtn = $status;
    // }

    // protected function trashBtn($status = true)
    // {
    //     return $this->_trashBtn = $status;
    // }

    // protected function addBtn($status = true)
    // {
    //     return $this->_addBtn = $status;
    // }

    // protected function getMaster()
    // {
    //     if (Request::ajax()) {
    //         $id = Request::input('id');
    //         if (!empty($id)) {

    //             #GET DATA
    //             $data = $this->_table
    //                         ->where($this->_pk, $id);

    //             #CEK DATA FOUND
    //             if($data->count() == 0){
    //                 $res['api_status']  = 0;
    //                 $res['api_message'] = 'Data Not Found';
    //                 return response()->json($res);
    //             }

    //             #SUKSES
    //             $res['api_status']  = 1;
    //             $res['api_message'] = 'success';
    //             $res['data']        = $data->first();
    //             return response()->json($res);
    //         }

    //         return $this->_dataTable->make(true);
    //     }
    //     $data['title']           = $this->_title;
    //     $data['cols']            = $this->_cols;
    //     $data['forms']           = $this->_forms;
    //     $data['filters']         = $this->_filters;
    //     $data['addBtn']          = $this->_addBtn;
    //     $data['actionBtn']       = $this->_actionBtn;
    //     $data['trashBtn']        = $this->_trashBtn;
    //     $data['backBtn']         = $this->_backBtn;
    //     $data['trash']           = $this->_trash;
    //     $data['scriptAddPage']   = $this->_scriptAddPage;
    //     $data['scriptEditPage']  = $this->_scriptEditPage;
    //     $data['scriptClosePage'] = $this->_scriptClosePage;
        
    //     return EB::load('template','template/master', $data);
    // }

    // protected function getTrash()
    // {
    //     if (Request::ajax()) {
    //         return $this->_dataTable->make(true);
    //     }
    //     $data['title']           = $this->_title;
    //     $data['forms']           = [];
    //     $data['cols']            = $this->_cols;
    //     $data['filters']         = $this->_filters;
    //     $data['trashBtn']        = false;
    //     $data['addBtn']          = false;
    //     $data['actionBtn']       = false;
    //     $data['backBtn']         = true;
    //     $data['trash']           = true;
    //     $data['scriptAddPage']   = '';
    //     $data['scriptEditPage']  = '';
    //     $data['scriptClosePage'] = '';
        
    //     return EB::load('template','template/master', $data);
    // }

    // protected function filter($name, $label, $type, $diff = array(), $params = array())
    // {
    //     $this->_filter = [];
    //     $this->_filter = [
    //         'name'       => $name,
    //         'label'      => $label,
    //         'type'       => $type,
    //         'diff'       => $diff,
    //         'params'     => $params,
    //     ];
        
    //     return $this;
    // }
}

?>