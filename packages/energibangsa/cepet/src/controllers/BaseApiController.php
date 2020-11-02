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
class BaseApiController extends Controller
{
    protected function setValidator($validations) {
        $rules = [];
        $messages = [];
        
        foreach ($validations as $name => $validation) {
            $rules[$name] = '';
                foreach($validation as $key => $value) {
                    $rules[$name] .= $key.'|'; 
                    preg_match('/^[A-z]+/', $key, $msg_name);
                    $messages[$name.'.'.$msg_name[0]] = $value;
                }

            $rules[$name] = rtrim($rules[$name],'|');    
        }  
        return $this->validator($rules, $messages);
    }

    protected function validator($rules, $messages = null)
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

    protected function output($response, $status = 200)
    {
        return response()->json($response, $status);
    }
}