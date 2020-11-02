<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Controller;
use Illuminate\Http\Request;
use App\Models\InventoriesUser;
use DB;

class InventoriesUserController extends Controller
{
    public function getIndex()
    {
        // return InventoriesUser::all();
        $data = DB::table('inventories_users')
                ->join('inventories', 'inventories_users.inventory_id', '=', 'inventories.id')
                ->join('users', 'inventories_users.user_id', '=', 'users.id')
                ->select('inventories_users.id','users.name', 'inventories.name as Product', 'inventories_users.price', 
                        'inventories_users.stocks', 'inventories.code', 'inventories.picts', 'inventories.descriptions')
                ->orderBy('inventories_users.id', 'asc')
                ->get();
        return response()->json([
            'message' => 'Retrieve Success!',
            'status' => 1,
            'data' => $data
        ]);
    }

    public function postAdd(Request $request)
    {
        $data = new InventoriesUser;
        $data->user_id = $request->user_id;
        $data->inventory_id = $request->inventory_id;
        $data->stocks = $request->stocks;
        $data->price = $request->price;

        $data->save();

        return response()->json([
            'message' => 'Add data succes!',
            'status' => 1,
            'data' => $data
        ]);
    }

    public function postDelete($id)
    {
        $data = InventoriesUser::find($id);
        $data->delete();

        return response()->json([
                'message' => 'Deleted data succesfully!',
                'status' => 1
            ]);
    }
}
