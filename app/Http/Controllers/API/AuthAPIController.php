<?php

namespace App\Http\Controllers\API;

use Energibangsa\Cepet\controllers\BaseApiController;
use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use JWTAuth; 
use Tymon\JWTAuth\Exceptions\JWTException; 
use DB;
use Hash;
use Request;
use Energibangsa\Cepet\helpers\EB;

class AuthAPIController extends BaseApiController
{
    public function getIndex()
    {
        return $this->output([
                'message' => 'Not Found',
                'status' => 0
            ], 404);
    }
    public function postLogin()
    {
        $this->setValidator([
            'username' => [
                'required' => 'Username wajib diisi',
            ],
            'password' => [
                'required' => 'Password Wajib diisi',
            ]
        ]);

        try {
            $respone['status']  = 0;

            $credentials = request(['username', 'password']);
            $credentials = array_merge($credentials, ['deleted_at' => null]);
            if (! $token = JWTAuth::attempt($credentials)) {
                $respone['status']  = 0;
                $respone['message'] = 'Username dan Password tidak valid!';
                return $this->output($respone, 401);
            }
        } catch (JWTException $e) {
            $respone['status']  = 0;
            $respone['message'] = 'Terjadi Masalah';
            return $this->output($respone, 500);
        }

        $user = DB::table('users')->select('privileges.name as privilege_name', 'users.*')
            ->join('privileges', 'privileges.id','=','users.privilege_id')
            ->where('username',JWTAuth::user()->username)
            ->first();
            
        // $user->upline = DB::table('users')
        //                     ->select('id', 'users.name as name', 'photo', 'code', 'phone', 'address', 'expired', 'email')
        //                     ->where('code', $user->referral_code)
        //                     ->whereNotNull('code')
        //                     ->first();
                            
        // $user->membership = DB::table('users_subscriptions')->where('stockist_code', $user->stockist_code ?? $user->code)->first()->membership ?? null;

        $respone['status']       = 1;
        $respone['message']      = 'Login Berhasil';
        $respone['access_token'] = $token;
        $respone['data']         = $user;

        return $this->output($respone, 200);
    }

    public function getLogout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->output([
            'status' => 1,
            'message' => 'Berhasil Logout'
        ]);
    }

    public function getCheckUpline()
    {
        $code = request('code');

        $upline = DB::table('users')->where('code', $code)->where('privilege_id',  '<>', 1);

        if ($upline->count() == 0) {
            return $this->output([
                'status' => 0,
                'message' => 'Code Upline tidak tersedia',
            ]);
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $upline->first(),
        ]);
    }

    public function postRegister()
    {
        $this->setValidator([
            'name' => [
                'required' => 'Nama wajib diisi',
            ],
            'privilege_id' => [
                'required' => 'Jenis Member wajib diisi',
            ],
            'phone' => [
                'required' => "Nomor Telepon wajib diisi",
            ],
            'username' => [
                'required' => 'Username wajib diisi',
            ],
            'password' => [
                'required' => "Password wajib diisi",
                'confirmed' => "Password konfirmasi berbeda",
            ],
        ]);
        
        $postdata = request()->all();

        unset($postdata['password_confirmation']);
        $postdata['password'] = Hash::make($postdata['password']);

        if ($postdata['privilege_id'] == 3) {
            $referral = DB::table('users')->where('code', $postdata['referral_code'])->first();
            $postdata['stockist_code'] = $referral->stockist_code ?? $referral->code;
        }

        $insert = EB::insert('users', $postdata);

        if (!$insert) {
            return $this->output([
                'status' => 0,
                'message' => 'gagal memasukkan ke database',
            ]);
        }
        
        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => $insert,
        ]);
    }
}
