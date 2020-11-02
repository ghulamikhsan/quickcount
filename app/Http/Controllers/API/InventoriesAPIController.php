<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Energibangsa\Cepet\controllers\BaseApiController;


use DB;
use JWTAuth;

class InventoriesAPIController extends BaseApiController
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

    public function getProducts($id = null)
    {
        $prefix = DB::getTablePrefix();
        $search = request('search');

        $data = DB::table('inventories')
        ->leftJoin('categories', 'inventories.category_id', '=', 'categories.id')
        ->leftJoin('inventories_users', function($join) {
            $join->on('inventories_users.inventory_id', '=', 'inventories.id')
                ->where('user_id', JWTAuth::user()->id);
        })
        ->select( 'inventories.id', 'inventories.name', 'categories.name as category_name',
                    'categories.id as category_id', 'inventories.code', DB::raw("CONCAT('".url("/storage")."/',".$prefix."inventories.picts) as picts"), 
                    'inventories.descriptions', 
                    'end_price as price',
                    DB::raw('(IFNULL('.$prefix.'inventories_users.instock, 0) - IFNULL('.$prefix.'inventories_users.outstock,0)) as stocks')
                )
        ->whereNull('inventories.deleted_at');

        if ($search) {
            $data = $data->where(function($query) use ($search) {
                $query->where('inventories.name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($id === null) {
            $data = $data->orderBy('stocks','desc')->paginate(10);
        } else {
            $data = $data->where('inventories.id', $id)->first();
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function getCategories($id = null)
    {
        $data =  DB::table('categories')
                            ->select('id', 'name')
                            ->whereNull('deleted_at');
        
        if ($id === null) {
            $data = $data->get();
        } else {
            $prefix = DB::getTablePrefix();
            $data = DB::table('inventories')
                ->leftJoin('categories', 'inventories.category_id', '=', 'categories.id')
                ->leftJoin('inventories_users', function($join) {
                    $join->on('inventories_users.inventory_id', '=', 'inventories.id')
                        ->where('user_id', JWTAuth::user()->id);
                })
                ->select( 'inventories.id', 'inventories.name', 'categories.name as category_name',
                            'categories.id as category_id', 'inventories.code', DB::raw("CONCAT('".url("/storage")."/',".$prefix."inventories.picts) as picts"), 
                            'end_price as price', 'inventories.descriptions',
                            DB::raw('IFNULL(('.$prefix.'inventories_users.instock - '.$prefix.'inventories_users.outstock),0) as stocks')
                )
                ->whereNull('inventories.deleted_at')
                ->where('categories.id', $id)
                ->orderBy('stocks', 'desc')
                ->get();
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data,
        ]);
    }

    // public function postEditProduct()
    // {
    //     $this->setValidator([
    //         'id' => [
    //             'required' => 'Inventory ID Wajib diisi', 
    //         ],
    //         'price' => [
    //             'required' => 'Harap isi harga barang',
    //         ],
    //         'stock' => [
    //             'required' => 'Harap isi stock barang',
    //         ],
    //     ]);

    //     $postdata = request()->all();

    //     $product = DB::table('inventories_users')
    //                 ->where('id', $postdata['id'])
    //                 ->where('user_id', JWTAuth::user()->id);
    //     if ($product->count() == 0) {
    //         return $this->output([
    //             'status' => 0,
    //             'message' => 'Produk tidak ditemukan'
    //         ]);
    //     }

    //     $product_data = $product->first();
    //     $stock_now    = $product_data->instock - $product_data->outstock;
    //     $instock      = 0;

    //     if ($stock_now < $postdata['stock']) {
    //         $instock = $stock_now + ($postdata['stock'] - $stock_now);
    //     } else {
    //         $instock = $stock_now - ($stock_now - $postdata['stock']);
    //     }

    //     $product->update([
    //         'price' => $postdata['price'],
    //         'instock' => $instock,
    //     ]);

    //     $prefix = DB::getTablePrefix();
    //     return $this->output([
    //         'status' => 1,
    //         'message' => 'Produk berhasil diubah',
    //         'data' => DB::table('inventories')
    //                 ->leftJoin('categories', 'inventories.category_id', '=', 'categories.id')
    //                 ->leftJoin('inventories_users', function($join) {
    //                     $join->on('inventories_users.inventory_id', '=', 'inventories.id')
    //                         ->where('user_id', JWTAuth::user()->id);
    //                 })
    //                 ->select( 'inventories.id', 'inventories.name', 'categories.name as category_name',
    //                             'categories.id as category_id', 'inventories.code', 'inventories.picts', 
    //                             'inventories.descriptions', 
    //                             'inventories_users.price as price',
    //                             DB::raw('('.$prefix.'inventories_users.instock - '.$prefix.'inventories_users.outstock) as stocks')
    //                 )
    //                 ->where('inventories_users.id', $postdata['id'])
    //                 ->where('user_id', JWTAuth::user()->id)
    //                 ->first(),
    //     ]);
    // }
}
