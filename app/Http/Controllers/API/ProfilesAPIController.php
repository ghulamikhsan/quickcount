<?php

namespace App\Http\Controllers\API;

use Energibangsa\Cepet\controllers\BaseApiController;

use Energibangsa\Cepet\helpers\EB;

use DB;
use JWTAuth;
use Hash;
use Request;

class ProfilesAPIController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {   
        $data = DB::table('users')->where('id', JWTAuth::user()->id)->first();
        $data->photo = EB::getImage($data->photo);
        $point = DB::table('inventories')->join('inventories_users', function($join) use ($data) {
                $join->on('inventories.id', '=', 'inventories_users.inventory_id')
                    ->where('user_id', $data->id);
            })
            ->select(DB::raw('SUM((outstock-close)*pv) as point'))->first()->point;
            
        $data->point = $point;
        $data->upline = DB::table('users')
                            // ->select('id', 'users.name as name', 'photo', 'code', 'phone', 'address', 'expired', 'email')
                            ->where('code', $data->referral_code)
                            ->whereNotNull('code')
                            ->first();
        if ($data->upline) {
            $data->upline->photo = EB::getImage($data->upline->photo);
        }
        $data->stockist = DB::table('users')
                            ->where('code', $data->stockist_code)
                            ->whereNotNull('code')
                            ->first();

        $data->membership = DB::table('users_subscriptions')->where('stockist_code', $data->stockist_code ?? $data->code)->first()->membership ?? null;
        

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function postProfile()
    {
        // Validasi
        $this->setValidator([
            'name' => [
                'required' => 'Nama wajib diisi',
            ],
            'phone' => [
                'required' => "Nomor Telepon wajib diisi",
            ],
            'address' => [
                'required' => 'Alamat wajib diisi',
            ],
            'expired' => [
                'required' => 'masa berlaku wajib diisi',
            ],
            'email' => [
                'required' => 'Email wajib diisi',
                'unique:users,email,'.JWTAuth::user()->id => 'Email sudah ada',
            ],
            'password' => [
                'confirmed' => "Password konfirmasi berbeda",
            ],
            'ktp_number' => [
                'required' => 'Nomor KTP wajib diisi',
            ],
            'date_of_birth' => [
                'required' => 'Tanggal lahir wajib diisi',
            ],
            'gender' => [
                'required' => 'Jenis kelamin wajib diisi',
            ],
            'marital_status' => [
                'required' => 'Status pernikahan wajib diisi',
            ],

            'nama_pasangan' => [
                'required_if:marital_status,1' => 'Nama Pasangan wajib diisi',
            ],
            'date_of_birth_pasangan' => [
                'required_if:marital_status,1' => 'Tanggal Lahir Pasangan wajib diisi',
            ],
            'heir_name' => [
                'required' => 'Nama ahli waris wajib diisi',
            ],
            'heir_date_of_birth' => [
                'required' => 'Tanggal Lahir ahli waris wajib diisi',
            ],
            'heir_relationship' => [
                'required' => 'Hubungan ahli waris wajib diisi',
            ],
            'bank_account_name' => [
                'required' => "Name pemilik bank wajib diisi",
            ],
            'bank_name' => [
                'required' => 'Nama bank wajib diisi',
            ],
            'bank_cabang' => [
                'required' => "Cabang Bank Wajib diisi",
            ],
        ]);
        
        // Update
        $postdata = request()->all();
        unset($postdata['password_confirmation']);
        !empty($postdata['password']) && $postdata['password'] = Hash::make($postdata['password']);
        $postdata['updated_at'] = date('Y-m-d H:i:s');
        
        if (Request::file('photo')) {
            $upload = EB::uploadFile('photo', 'public/users', null, ['allowed_type' => 'jpeg|png|jpg'] );
            if (!$upload['status']) {
                return $this->output([
                    'status' => 0,
                    'message' => $upload['message'],
                ]);
            } else {
                $postdata['photo'] = $upload['filename'];
            }
        }

        $data = DB::table('users')->where('id', JWTAuth::user()->id);
        $data->update($postdata);
        $data = $data->first();
        $data->photo = EB::getImage($data->photo);

        // return success
        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data,
        ]);
    }
    
    public function getShipments($id = null)
    {
        $data = DB::table('shipments')->where('user_id', JWTAuth::user()->id);

        $data = ($id !== null ? $data->where("id", $id)->first() : $data->get());

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function postShipments()
    {
        // Validasi
        $this->setValidator([
            'province' => [
                'required' => 'Provinsi wajib diisi', 
            ],
            'province_id' => [
                'required' => 'ID Provinsi wajib diisi',
            ],
            'city' => [
                'required' => 'Kota wajib diisi',
            ],
            'city_id' => [
                'required' => 'ID Kota wajib diisi',
            ],
            'address' => [
                'required' => 'Alamat wajib diisi',
            ],
        ]);

        // Validasi API Rajaongkir

        $data = [
            "user_id" => JWTAuth::user()->id,
            "province" => request('province'),
            "province_id" => request('province_id'),
            "city" => request('city'),
            "city_id" => request('city_id'),
            "subdistrict" => request('subdistrict'),
            "subdistrict_id" => request('subdistrict_id'),
            "address" => request('address'),
            "used" => request('used') ?? 0,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        ];
                            
        $shipment_id = DB::table('shipments')->insertGetId($data);

        if ($data['used'] == 1) {
            DB::table('shipments')
                ->where('user_id', JWTAuth::user()->id)
                ->where('id', '<>', $shipment_id)
                ->update(['used' => 0]);
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => DB::table('shipments')->where('id', $shipment_id)->first(),
        ]);
    }

    public function getSales()
    {
        $data = DB::table('inventories_users')
                    ->leftJoin('inventories', 'inventories.id', '=', 'inventories_users.inventory_id')
                    ->where('user_id', JWTAuth::user()->id)
                    ->select('inventory_id', 'name', 'code', DB::raw("CONCAT('".url("/storage")."/',inventories.picts) as picts"), 'descriptions', DB::raw("(outstock-close) as qty"), 'pv', DB::raw("(outstock-close)*pv as point"))
                    ->orderBy('point', 'desc')
                    ->paginate(15);
        
        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => $data,
        ]);

    }

    public function getDownlines()
    {
        $user_id = request('user_id') ?? JWTAuth::user()->id;
        $user = DB::table('users')->where('id', $user_id)->first();
        $downlines = DB::table('users')
                        ->leftJoin('inventories_users', 'inventories_users.user_id', '=', 'users.id')
                        ->leftJoin('inventories', 'inventories.id', '=', 'inventories_users.inventory_id')
                        ->where('referral_code', $user->code)
                        ->select('users.id', 'users.name', 'users.code', DB::raw('SUM((outstock-close)*pv) as point'))
                        ->groupBy('users.id', 'users.name', 'users.code')
                        ->get();
        
        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $downlines,
        ]);
    }

    public function postDownlines()
    {

        $this->setValidator([
            'name' => [
                'required' => 'Nama wajib diisi',
            ],
            'code' => [
                'required' => 'Kode Member wajib diisi',
                'unique:users,code' => 'Kode Member sudah ada',
            ],
            'phone' => [
                'required' => "Nomor Telepon wajib diisi",
            ],
            'address' => [
                'required' => 'Alamat wajib diisi',
            ],
            'expired' => [
                'required' => 'masa berlaku wajib diisi',
            ],
            'email' => [
                'required' => "Email wajib diisi",
            ],
            'ktp_number' => [
                'required' => 'Nomor KTP wajib diisi',
            ],
            'date_of_birth' => [
                'required' => 'Tanggal lahir wajib diisi',
            ],
            'gender' => [
                'required' => 'Jenis kelamin wajib diisi',
            ],
            'marital_status' => [
                'required' => 'Status pernikahan wajib diisi',
            ],
            'nama_pasangan' => [
                'required_if:marital_status,1' => 'Nama Pasangan wajib diisi',
            ],
            'date_of_birth_pasangan' => [
                'required_if:marital_status,1' => 'Tanggal Lahir Pasangan wajib diisi',
            ],
            'heir_name' => [
                'required' => 'Nama ahli waris wajib diisi',
            ],
            'heir_date_of_birth' => [
                'required' => 'Tanggal Lahir ahli waris wajib diisi',
            ],
            'heir_relationship' => [
                'required' => 'Hubungan ahli waris wajib diisi',
            ],
            'bank_account_name' => [
                'required' => "Name pemilik bank wajib diisi",
            ],
            'bank_name' => [
                'required' => 'Nama bank wajib diisi',
            ],
            'bank_cabang' => [
                'required' => "Cabang Bank Wajib diisi",
            ],
        ]);

        $postdata = request()->all();
        $postdata['referral_code'] = JWTAuth::user()->code;
        $postdata['stockist_code'] = JWTAuth::user()->privilege_id == 2 ? JWTAuth::user()->code : JWTAuth::user()->stockist_code;
        $postdata['password'] = Hash::make(123456);
        $postdata['created_at'] = date('Y-m-d H:i:s');
        $postdata['updated_at'] = date('Y-m-d H:i:s');
        $postdata['created_by'] = JWTAuth::user()->id;
        $postdata['privilege_id'] = 3;

        $insert = DB::table('users')->insert($postdata);

        if (!$insert) {
            return $this->output([
                'status' => 0,
                'message' => 'gagal memasukkan ke database',
            ]);
        }

        $id_user = DB::getPdo()->lastInsertId();

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => DB::table("users")->where('id', $id_user)->first()
        ]);
    }

    public function getReport($code = null)
    {
        if ($code != null) {
            $myUser = DB::table("users")->where('code', $code);
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

        $transaction[0] = $this->getClosingPoint($myUser->id, $myUser->name, $myUser->code, $myUser->referral_code);

        $last_code[] = $myUser->code;

        while(isset($last_code[0])) {
            $downlines = DB::table('users')->where('referral_code', $last_code[0])->get();
            foreach ($downlines as $downline) {
                $transaction[] = $this->getClosingPoint($downline->id, $downline->name, $downline->code, $downline->referral_code);
                $last_code[] = $downline->code;
            }
            $last_code = array_values(array_diff($last_code, [$last_code[0]]));
        }

        if ($code != null) {
            $data['transactions'] = $transaction;
            $data['month'] = EB::bulan(date('m'));
            $data['year'] = date("Y");
            return view('profiles/report_point_excel', $data);
        }
        
        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => $transaction,
        ]);
    }
}
