<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseApiController;


use DB;
use JWTAuth;

class SuppliersAPIController extends BaseApiController
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

    public function getSupplierOld($code = null)
    {
        $additional_supplier = DB::table('additional_suppliers')
                                ->where('user_id', JWTAuth::user()->id)
                                ->whereNull('deleted_at');
        if ($code !== null) {
            $additional_supplier = $additional_supplier->where('code', $code);
        }
        $additional_supplier = $additional_supplier->select('code', 'name', DB::raw('0 as status'));

        $suppliers = DB::table('users')
                        ->where('code', JWTAuth::user()->referral_code)
                        ->where('privilege_id', '<>', '1');
                        if ($code !== null) {
                            $suppliers = $suppliers->where('code', $code);
                        }
        $suppliers = $suppliers->select('code', 'name', DB::raw('1 as status'))
                        ->union($additional_supplier);
        
        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $suppliers->get(),
        ]);
    }

    public function getSupplier($code = null)
    {
        $suppliers = DB::table('users')
                        ->where('code', JWTAuth::user()->referral_code)
                        ->where('privilege_id', '<>', '1');
        if ($code !== null) {
            $suppliers = $suppliers->where('code', $code);
        }
        $suppliers = $suppliers->select('code', 'name');
        
        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $suppliers->get(),
        ]);
    }

    public function getProducts()
    {
        $stockist_code = JWTAuth::user()->privilege_id == 3 ? JWTAuth::user()->stockist_code : JWTAuth::user()->code;
        $supplier = DB::table('users')->where('code', $stockist_code)->where('privilege_id','<>', 1)->first();
        
        $prefix = DB::getTablePrefix();
        $search = request('search');

        $inventories = DB::table('inventories')
                        ->leftJoin('categories', 'inventories.category_id', '=', 'categories.id')
                        ->leftJoin('inventories_users', function($join) use ($supplier) {
                            $join->on('inventories_users.inventory_id', '=', 'inventories.id')
                                ->where('user_id', $supplier->id);
                        })
                        ->select('inventories.id', 
                            'inventories.name', 
                            'categories.name as category_name',
                            'categories.id as category_id', 
                            'inventories.code', 
                            DB::raw("CONCAT('".url("/storage")."/',".$prefix."inventories.picts) as picts"),
                            'inventories.descriptions',
                            'member_price as price'
                        );
        if (JWTAuth::user()->privilege_id == 3) {
            $inventories->addSelect(DB::raw('IFNULL(('.$prefix.'inventories_users.instock - '.$prefix.'inventories_users.outstock),0) as stocks'));
        } else {
            $inventories->addSelect(DB::raw('99 as stocks'));
        }

        if ($search) {
            $inventories = $inventories->where(function($query) use ($search) {
                $query->where('inventories.name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            });
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $inventories->whereNull('inventories.deleted_at')->paginate(10),
        ]);
    }

    public function postSupplier()
    {
        $this->setValidator([
            'name' => [
                'required' => 'Nama Supplier wajib diisi', 
            ],
            'code' => [
                'required' => 'Kode Supplier Wajib diisi',
                'unique:additional_suppliers,code' => 'Kode Supplier sudah ada',
            ]
        ]);

        $postdata = request()->all();

        DB::table('additional_suppliers')->insert([
            'name' => $postdata['name'],
            'code' => $postdata['code'],
            'user_id' => JWTAuth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->output([
            'status' => 1,
            'message' => 'Sukses menambahkan supplier'
        ]);
    }

    public function postEditSupplier()
    {
        $this->setValidator([
            'code' => [
                'required' => 'Kode Supplier wajib diisi',
                'exists:additional_suppliers,code' => 'Tidak terdapat kode supplier',
            ],
            'name' => [
                'required' => 'Nama Supplier wajib diisi', 
            ],
        ]);

        $postdata = request()->all();

        $additional_supplier = DB::table('additional_suppliers')
            ->where('code', $postdata['code'])
            ->where('user_id', JWTAuth::user()->id);

        if ($additional_supplier->count() == 0) {
            return $this->output([
                'status' => 0,
                'message' => 'Tidak terdapat data tersebut'
            ]);
        }

        $additional_supplier->update([
            'name' => $postdata['name'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->output([
            'status' => 1,
            'message' => 'Sukses mengubah supplier',
            'data' => $additional_supplier->first(),
        ]);
    }

    public function postDeleteSupplier()
    {
        $code = request('code') ?? null;
        $additonal_supplier = DB::table('additional_suppliers')
                                ->where("code", $code)
                                ->where('user_id', JWTAuth::user()->id);

        if ($additonal_supplier->count() == 0) {
            return $this->output([
                'status' => 0,
                'message' => 'Kode Supplier tidak ada',
            ]);
        }

        $additonal_supplier->update([
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        return $this->output([
            'status' => 1,
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
