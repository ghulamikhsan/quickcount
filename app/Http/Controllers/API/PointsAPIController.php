<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseApiController;

use Energibangsa\Cepet\helpers\EB;

use DB;
use JWTAuth;

class PointsAPIController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {   
        return $this->output([
            'status' => 0,
            'message' => 'Not Found',
        ], 404);
    }

    public function getReports($stockist_code = null)
    {
        // Get User
        if ($stockist_code != null) {
            $myUser = DB::table("users")->where('code', $stockist_code)->where('privilege_id', 2);
            if ($myUser->count() == 0) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Tidak terdapat kode tersebut'
                ], 404);
            }

            $myUser = $myUser->first();
        } else {
            $myUser = JWTAuth::user();
        }

        $transaction = [];

        // Get All User with history point in month and year
        $users = DB::table('users')
                            ->where('code', $myUser->code)
                            ->orWhere('stockist_code', $myUser->code)
                            ->whereNull('deleted_at')
                            ->orderBy('id', 'asc')
                            ->get();
        
        foreach ($users as $user) {
            $temp = [];
            $user_history = DB::table('history_points')
                                ->where([
                                    ['user_id', '=', $user->id],
                                    ['month', '=', date('m')],
                                    ['year', '=', date('Y')],
                                ])
                                ->first();
                                // ->leftJoin('history_point_details', 'history_points.id', '=', 'history_point_details.history_point_id')
                                // ->leftJoin('inventories', 'inventories.id', '=', 'history_point_details.inventory_id')
                                // ->select('history_points.*', 'history_points.id as id', 'history_point_details.*', 'inventories.name as inventory_name', 'inventories.code as inventory_code')
                                // ->get();
                                
            $temp[$user->id]['id'] = $user_history->id ?? null;
            $temp[$user->id]['name'] = $user->name;
            $temp[$user->id]['code'] = $user->code;
            $temp[$user->id]['referral_code'] = $user->referral_code;
            $temp[$user->id]['self_point'] = $user_history->self_point ?? 0;
            $temp[$user->id]['group_point'] = $user_history->group_point ?? 0;
            $temp[$user->id]['status'] = (int) ($user_history->status ?? 0);
            
            if (!isset($user_history->id)) {
                $temp[$user->id]['details'] = null;
            } else {
                $user_history_details = DB::table("history_point_details")
                                            ->where('history_point_id', $user_history->id)
                                            ->leftJoin('inventories', 'inventories.id', '=', 'history_point_details.inventory_id')
                                            ->select('history_point_details.*', 'inventories.name as inventory_name', 'inventories.code as inventory_code')
                                            ->get();

                foreach ($user_history_details as $key => $details) {
                    $temp[$user->id]['details'][$key]['inventory_code'] = $details->inventory_code;
                    $temp[$user->id]['details'][$key]['inventory_name'] = $details->inventory_name;
                    $temp[$user->id]['details'][$key]['qty'] = $details->qty;
                    $temp[$user->id]['details'][$key]['pv'] = $details->pv;
                    $temp[$user->id]['details'][$key]['point'] = $details->point;
                }
            }
            
            $transaction[] = $temp[$user->id];
        }

        // dd($transaction);

        // Get Data

        // $transaction[0] = $this->getClosingPoint($myUser->id, $myUser->name, $myUser->code, $myUser->referral_code);

        // $last_code[] = $myUser->code;

        // while(isset($last_code[0])) {
        //     $downlines = DB::table('users')->where('referral_code', $last_code[0])->get();
        //     foreach ($downlines as $downline) {
        //         $transaction[] = $this->getClosingPoint($downline->id, $downline->name, $downline->code, $downline->referral_code);
        //         $last_code[] = $downline->code;
        //     }
        //     $last_code = array_values(array_diff($last_code, [$last_code[0]]));
        // }

        if ($stockist_code != null) {
            $data['transactions'] = $transaction;
            $data['month'] = EB::bulan(date('m'));
            $data['year'] = date("Y");
            // dd($data);
            return view('profiles/report_point_excel', $data);
        }
        
        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => $transaction,
        ]);
    }

    private function getClosingPoint($user_id, $name, $code, $referral_code = null)
    {
        $history = $this->getHistoryPoints($user_id, null, date('m'), date('Y'));
        $data['name'] = $name;
        $data['code'] = $code;
        $data['referral_code'] = $referral_code;
        $data['self_point'] = $history->self_point ?? 0;
        $data['group_point'] = $history->group_point ?? 0;
        $data['details'] = $history->detail ?? null;

        return $data;
    }

    private function getHistoryPoints($user_id = null, $id = null, $month = null, $year = null)
    {
        $user_id = $user_id ?? JWTAuth::user()->id;

        $data = DB::table('history_points')
                ->where('user_id', '=', $user_id);

        // if ($id || ($month && $year)) {
            if ($id) {
                $data = $data->where('id', '=', $id);
            } elseif (!empty($month) && !empty($year)) {
                $data->where('month', date('m'))
                    ->where('year', date('Y'));
            }

            if ($data->count() > 0) {
                $data = $data->first();
                
                $data->detail = DB::table('history_point_details')
                                    ->join('inventories', 'history_point_details.inventory_id', '=', 'inventories.id')
                                    ->where('history_point_id', $data->id)
                                    ->select("inventories.name", "inventories.code", 'history_point_details.*')
                                    ->get();
            } else {
                $data = null;
            }
        // } else {
            // $data = $data->paginate(15);
        // }

        return $data;
    }

    public function getHistoryPoint()
    {
        $id = request('id') ?? null;
        $user_id = request('user_id') ?? JWTAuth::user()->id;
        $data = DB::table('history_points')
                ->where('user_id', '=', $user_id);
        $prefix = DB::getTablePrefix();

        if ($id) {
            $data = $data->where('id', '=', $id);
            if ($data->count() == 0) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Tidak terdapat data tersebut'
                ]);
            }
            $data = $data->first();
            $data->detail = DB::table('history_point_details')
                                ->leftJoin('inventories', 'inventories.id', '=', 'history_point_details.inventory_id')
                                ->where('history_point_id', $data->id)
                                ->select('history_point_details.*', 'inventories.name', DB::raw("CONCAT('".url("/storage")."/',".$prefix."inventories.picts) as picts"))
                                ->get();
        } else {
            $data = $data->paginate(15);
        }

        $total_point = DB::table('history_points')
                ->where('user_id', '=', $user_id)
                ->select(DB::RAW('SUM(self_point) + SUM(group_point) as total_point'))->first()->total_point;

        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => $data,
            'total_point' => $total_point,
        ]);
    }

    public function postEditPoint()
    {
        // checking status
        $this->setValidator([
            'id' => [
                'required' => 'ID Wajib diisi',
                'exists:history_points,id' => 'Tidak terdapat data',
            ],
            'inventory_id' => [
                'required' => 'Produk Wajib diisi', 
                'exists:inventories,id' => 'Produk tidak terdapat pada inventory',
            ],
            'inventory_qty' => [
                'required' => 'Masukkan jumlah produk'
            ],
        ]);

        $id = request('id');
        $inventories = request('inventory_id');
        $qty = request('inventory_qty');

        $history_point = DB::table('history_points')->where('id', $id);

        if ($history_point->count() == 0) {
            return $this->output([
                'status' => 0,
                'message' => 'Data tidak ditemukan',
            ], 403);
        }

        // if 2 cant edit
        if ($history_point->first()->status == 2) {
            return $this->output([
                'status' => 0,
                'message' => 'Data sudah ditutup, tidak dapat mengedit',
            ], 404);
        }

        // manipulate editing
        // edit history
        $total_point = 0;
        foreach ($inventories as $key => $inventory) {
            $current_data = DB::table('history_point_details')
                            ->where('inventory_id', $inventory)
                            ->where('history_point_id', $history_point->first()->id);
            
            // jika ada pengurangan
            if ($current_data->first()->qty > $qty[$key]) {
                // perbarui history point details
                $current_data->update([
                    'qty' => $qty[$key]
                ]);

                // kurangi close inventory_users
                DB::table('inventories_users')
                    ->where('inventory_id', $inventory)
                    ->where('user_id', $history_point->first()->user_id)
                    ->update([
                        'close' => DB::raw('close - '. ($current_data->first()->qty - $qty[$key])),
                    ]);
            }
            
            $point = $total_point + ($qty[$key] * $current_data->first()->pv);
        }
        // Hitung total point

        $history_point->update([
            'self_point' => $point,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $history_point->first(),
        ]);
    }

    public function getSales()
    {
        $data = DB::table('inventories_users')
                    ->leftJoin('inventories', 'inventories.id', '=', 'inventories_users.inventory_id')
                    ->where('user_id', JWTAuth::user()->id)
                    ->select('inventory_id', 'name', 'code', DB::raw("CONCAT('".url("/storage")."/',picts) as picts"), 'descriptions', DB::raw("(outstock-close) as qty"), 'pv', DB::raw("(outstock-close)*pv as point"))
                    ->orderBy('point', 'desc')
                    ->paginate(15);
        
        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => $data,
        ]);

    }

    // Tukar Point
    public function postTupo()
    {
        $check_membership = DB::table('users_subscriptions')->where('stockist_code', JWTAuth::user()->stockist_code ?? JWTAuth::user()->code);
        if ($check_membership->count() == 0) {
            // error
            return $this->output([
                'status' => 0,
                'message' => 'Hanya member berbayar yang bisa mengakses fitur ini',
            ]);
        } else {
            $check_membership = $check_membership->first();
            if ($check_membership->membership <= date('Y-m-d')) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Maaf, masa member berbayar anda telah habis',
                ], 500);
            }
        }
        // Validasi
        $this->setValidator([
            'inventories' => [
                'required' => 'ID Inventory wajib diisi', 
                'exists:inventories,id' => 'Product ID tidak tersedia'
            ],
            'qty' => [
                'required' => 'ID Provinsi wajib diisi',
            ],
        ]);

        $inventories = request('inventories');
        $qty         = request('qty');
        $user_id     = JWTAuth::user()->id;
        $point       = 0;
        $inv_details = [];

        $check_close = DB::table('history_points')->where([
            ['user_id', '=', JWTAuth::user()->id],
            ['month', '=', date('m')],
            ['year', '=', date('Y')],
            ['status', '=', 2],
        ]);

        if ($check_close->count() > 0) {
            return $this->output([
                'status' => 0,
                'message' => 'Anda telah melakukan tutup point',
            ], 403);
        }

        DB::beginTransaction();
        foreach ($inventories as $key => $inventory) {
            $inv_users = DB::table('inventories_users')
                        ->where('user_id', $user_id)
                        ->where('inventory_id', $inventory);

            $my_inventory = $inv_users->first();
            if(($my_inventory->outstock - $my_inventory->close) < $qty[$key]) {
                DB::rollBack();
                return $this->output([
                    'status' => 0,
                    'message' => 'Jumlah Point tidak benar'
                ]);
            }
            
            $inv = DB::table('inventories')->where('id', $inventory)->first();
            $point += $inv->pv*$qty[$key];

            $inv_details[] = [
                'inventory_id' => $inventory,
                'qty' => $qty[$key],
                'pv' => $inv->pv,
                'point' => $inv->pv*$qty[$key],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $inv_users->update([
                'close' => DB::raw('close + '.$qty[$key])
            ]);
        }

        $check_exists = DB::table('history_points')->where([
            ['user_id', '=', JWTAuth::user()->id],
            ['month', '=', date('m')],
            ['year', '=', date('Y')],
        ]);

        $check_manager = JWTAuth::user()->referral_code == JWTAuth::user()->stockist_code;

        if ($check_exists->count() > 0) {
            $current_point = $check_exists->first()->self_point;
            $total_point = $current_point + $point;
            if (( JWTAuth::user()->privilege_id == 2 || $check_manager) && $total_point < 100 ) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Minimal Tutup Point adalah 100 Point',
                    'point' => $point,
                ], 403);
            } elseif ($total_point < 50) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Minimal Tutup Point adalah 50 Point',
                    'point' => $point,
                ], 403);
            }

            $check_exists->update([
                'self_point' => DB::raw('self_point + '.$point),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $id_history_point = $check_exists->first()->id;
        } else {

            if (( JWTAuth::user()->privilege_id == 2 || $check_manager) && $point < 100 ) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Minimal Tutup Point adalah 100 Point',
                    'point' => $point,
                ], 403);
            } elseif ($point < 50) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Minimal Tutup Point adalah 50 Point',
                    'point' => $point,
                ], 403);
            }
            
            DB::table('history_points')->insert([
                'user_id' => $user_id,
                'self_point' => $point,
                'month' => date('m'),
                'year' => date('Y'),
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $id_history_point = DB::getPdo()->lastInsertId();
        }

        foreach ($inv_details as $details) {
            $history_point_detail = DB::table('history_point_details')
                                        ->where([
                                            ['history_point_id', '=', $id_history_point],
                                            ['inventory_id', '=', $details['inventory_id']]
                                        ]);
                
            if ($history_point_detail->count() > 0) {
                $history_point_detail->update([
                    'qty' => DB::raw('qty + '.$details['qty']),
                ]);
            } else {
                DB::table('history_point_details')->insert([
                    'history_point_id' => $id_history_point,
                    'inventory_id' => $details['inventory_id'],
                    'qty' => $details['qty'],
                    'pv' => $details['pv'],
                    'point' => $details['point'],
                    'created_at' => $details['created_at'],
                    'updated_at' => $details['updated_at'],
                ]);
            }
        };

        // Notification to stockist

        DB::commit();

        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'point' => $point,
        ]);
    }

    // Confirm Member change to 1
    public function postConfirmationMember()
    {
        $this->setValidator([
            'id' => [
                'required' => "ID Wajib diisi",
                'exists:history_points,id' => "Data tidak ditemukan",
            ],
        ]);

        $id = request('id');

        // change status
        $history_point = DB::table('history_points')->where('id', $id);
        $history_point->update([
            'status' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // send notif to member

        // success
        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => $history_point->first(),
        ]);
    }

    // Member Konfirmasi Point change to 2
    public function getConfirmationClose()
    {
        $id = request('id');

        if ($id) {
            // checking
            $history_point = DB::table('history_points')->where('id', $id);
            if ($history_point->count() == 0 ) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Data tidak ditemukan',
                ], 403);
            }

            // Close Point
            $point = $history_point->first()->self_point;
            $history_point->update([
                'status' => 2,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Point Group
            $user = DB::table('users')
                        ->where("id", $history_point->first()->user_id)
                        ->whereNull('deleted_at')
                        ->first();

            while ($user->referral_code) {
                $user_group = DB::table('users')
                                ->where('code', $user->referral_code)
                                ->first();

                $ug_history_point = DB::table('history_points')
                                ->where([
                                    ['user_id', '=', $user_group->id],
                                    ['month', '=', $history_point->first()->month],
                                    ['year', '=', $history_point->first()->year],
                                ]);

                // if no data user in history point
                if ($ug_history_point->count() == 0) {
                    DB::table('history_points')->insert([
                        'user_id' => $user_group->id,
                        'self_point' => 0,
                        'group_point' => $point,
                        'month' => date('m'),
                        'year' => date('Y'),
                        'status' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $ug_history_point->update([
                        'group_point' => DB::raw('group_point + ' . $point),
                    ]);
                }

                $user = $user_group;
            }
        } else {
            // checking
            $history_point = DB::table('history_points')
                            ->whereRaw('updated_at >= now() - INTERVAL 1 DAY')
                            ->where('status', 1);

            if ($history_point->count() == 0) {
                return $this->output([
                    'status' => 1,
                    'message' => 'tidak terdapat data',
                ], 403);
            }

            $history_point = $history_point->first();
            // Close Point
            $point = $history_point->self_point;
            $history_point->update([
                'status' => 2,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Point Group
            $user = DB::table('users')
                        ->where("id", $history_point->first()->user_id)
                        ->whereNull('deleted_at')
                        ->first();

            while ($user->referral_code) {
                $user_group = DB::table('users')
                                ->where('code', $user->referral_code)
                                ->first();

                $ug_history_point = DB::table('history_points')
                                ->where([
                                    ['user_id', '=', $user_group->id],
                                    ['month', '=', $history_point->first()->month],
                                    ['year', '=', $history_point->first()->year],
                                ]);

                // if no data user in history point
                if ($ug_history_point->count() == 0) {
                    DB::table('history_points')->insert([
                        'user_id' => $ug->id,
                        'self_point' => 0,
                        'group_point' => $point,
                        'month' => date('m'),
                        'year' => date('Y'),
                        'status' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $ug_history_point->update([
                        'group_point' => DB::raw('group_point + ' . $point),
                    ]);
                }

                $user = $user_group;
            }
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $history_point->first(),
        ]);
    }
}
