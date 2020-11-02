<?php

namespace App\Http\Controllers\API;

use Energibangsa\Cepet\controllers\BaseApiController;

use DB;
use JWTAuth;

class SubscriptionsAPIController extends BaseApiController
{
    // List Subscriptions
   public function getIndex()
   {
       $subscriptions = DB::table('subscriptions')->whereNull('deleted_at')->get();
       
       return $this->output([
           'status' => 1,
           'message' => 'success',
           'data' => $subscriptions
       ]);
   }

   // status my subscriptions
   public function getStatus()
   {
       $mySubsctripton = DB::table('subscription_users')
                            ->where('user_id', JWTAuth::user()->id)
                            ->where('active', 1)
                            ->leftJoin('subscriptions', 'subscriptions.id', '=', 'subscription_id')
                            ->select('name', 'month', 'subscription_users.created_at', DB::raw("IF(sns_subscription_users.created_at > now(), 'Expired', 'Active') as status"))
                            ->orderBy('created_at', 'desc')
                            ->first();

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $mySubsctripton
        ]);                            
   }

   // upgrade subscriptions
   public function postUpgrade()
   {
        $this->setValidator([
            'id' => [
                'required' => 'ID Tipe Subscription wajib diisi', 
            ]
        ]);

        $id = request('id');
        
        DB::table('subscription_users')
                ->insert([
                    'user_id' => JWTAuth::user()->id,
                    'subscription_id' => $id,
                    'active' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        
        return $this->output([
            'status' => 1,
            'message' => 'success'
        ]);
   }
}