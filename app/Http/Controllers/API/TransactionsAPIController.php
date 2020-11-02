<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Request;
use Energibangsa\Cepet\controllers\BaseApiController;
use Energibangsa\Cepet\helpers\EB;
use Illuminate\Database\QueryException;

use DB;
use JWTAuth;
use Log;

class TransactionsAPIController extends BaseApiController
{
    private $table = "transactions";
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

    public function getChart()
    {
        $user_id   = request('user_id') ?? JWTAuth::user()->id;

        $data = DB::table('vlaba_rugi')
                    ->orderBy('tahun', 'asc')
                    ->orderBy('bulan', 'asc')
                    ->where('user_id', $user_id)
                    ->limit(12)->get();

                    // foreach ($data as $key => $dt) {
        //     $data[$key]->bulan = EB::bulan($dt->bulan);
        // }

        // $count = count($data)-1;
        // foreach ($data as $key => $dt) {
        //     $data[$count] = $dt;
        //     $count--;
        // }


        return $this->output([
            'status' => 0,
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function getLabaRugi()
    {
        $startDate = request('start_date') ?? null;
        $endDate   = request('end_date') ?? null;
        $month     = request('month') ?? date('m');
        $year      = request('year') ?? date('Y');

        $data = DB::table('transactions')
                    ->where('user_id', JWTAuth::user()->id);
                    $data1 = $data;

        if (!empty($startDate) && !empty($endDate)) {
            $data->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', date('Y-m-d', strtotime($endDate . ' +1 day')));
        } 
        if (!empty($month) && !empty($year)) {
            $data->whereRaw("year(created_at) = $year AND month(created_at) = $month");
        }

        $pendapatan = clone $data;
        $pengeluaran = clone $data;

        $pendapatan = $pendapatan->where('type', 'sale')->select(DB::raw('IFNULL(SUM(total),0) as total'))->first()->total;
        $pengeluaran = $pengeluaran->where('type', 'buy')->select(DB::raw('IFNULL(SUM(total),0) as total'))->first()->total;

        $laba_rugi = $pendapatan - $pengeluaran;
        // dd(DB::getQueryLog()); // Show results of log


        return $this->output([
            'status' => 1,
            'message' => 'success',
            'date' => [
                'pendapatan' => $pendapatan,
                'pengeluaran' => $pengeluaran,
                'laba_rugi' => $laba_rugi
            ],
        ]);
    }

    public function getData($code = null)
    {
        $type      = request('type') ?? null;
        $startDate = request('start_date') ?? null;
        $endDate   = request('end_date') ?? null;
        $month     = request('month') ?? null;
        $year      = request('year') ?? null;
        $user_id   = request('user_id') ?? JWTAuth::user()->id;

        $data = DB::table($this->table)
                    ->where('user_id', $user_id)
                    // ->where('user_id', JWTAuth::user()->id)
                    // ->orWhere('supplier_code', JWTAuth::user()->code)
                    ->orderBy('created_at', 'desc');
        
        if (!empty($type)) {
            $data = $data->where('type', $type);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $data = $data->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', date('Y-m-d', strtotime($endDate . ' +1 day')));
        }

        if (!empty($month) && !empty($year)) {
            $data = $data->whereRaw("year(created_at) = $year AND month(created_at) = $month");
        }

        if ($code !== null) {
            $data = $data->where('code', $code);
            if ($data->count() == 0) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Tidak terdapat data tersebut',
                ]);
            }

            $data = $data->first();
            $data->details = DB::table('transaction_details')
                    ->leftJoin('inventories', 'transaction_details.inventory_id', '=', 'inventories.id')
                    ->where('transaction_id', $data->id)
                    ->select('transaction_details.*', DB::raw("CONCAT('".url("/storage")."/',sns_inventories.picts) as picts"), 'name')
                    ->get();
        } else {
            $data = $data->paginate(15);
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function postTransaction()
    {
        // Validasi
        $this->setValidator([
            'inventory_id' => [
                'required' => 'Produk Wajib diisi', 
                'exists:inventories,id' => 'Produk tidak terdapat pada inventory',
            ],
            'inventory_qty' => [
                'required' => 'Masukkan jumlah produk'
            ]
        ]);

        $postdata = Request::all();
        
        $type = ['sale', 'buy', 'sale return', 'buy return'];
        if (!in_array($postdata['type'], $type)) {
            return $this->output([
                'status' => 0,
                'message' => 'Terjadi kesalahan'
            ], 404);
        }

        // $myInventory = $this->checkSupplier($postdata['supplier_code'] ?? null);

        // varification shipment
        $postdata['payment_method'] = $postdata['payment_method'] ?? 'cod';
        if ($postdata['payment_method'] == 'transfer') {
            $shipment = DB::table('shipments')
                ->where('user_id', $myInventory['inventories_user_id'])
                ->where('used', 1)
                ->count();

            if ($shipment > 0) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Alamat Supplier tidak ada'
                ]);
            }
        }

        // Hitung total
        $total = $this->calculateTotal($postdata);
        
        // hitung discount
        $discount = $this->calculateDiscount($postdata['code_coupon'] ?? null, $total);

        DB::beginTransaction();
        try {
            // Insert Transaction
            $transaction_id = $this->insertTransaction(
                                [
                                    'type' => $postdata['type'],
                                    'user_id' => JWTAuth::user()->id,
                                    'supplier_code' => JWTAuth::user()->stockist_code,
                                    'code' => EB::invoiceNumber($postdata['type']),
                                    'total' => $total,
                                    'code_coupon' => $postdata['code_coupon'] ?? null,
                                    'discount_percent' => $discount['percent'],
                                    'discount_price' => $discount['price'],
                                    'discount' => $discount['total'],
                                    'status' => ($postdata['type'] == 'buy' && JWTAuth::user()->privilege_id == 3) ? 'menunggu' : 'selesai',
                                    'payment_method' =>  $postdata['payment_method'] ?? 'cod',
                                    'shipping_fee' => $postdata['shipping_fee'] ?? 0,
                                    'shipment' => $postdata['shipment'] ?? null,
                                    'service' => $postdata['service'] ?? null,
                                    'etd' => $postdata['etd'] ?? null,
                                    'grand_total' => $discount['grand_total'],
                                ], 
                                $postdata
                            );

            // insert Shipment
            if($postdata['payment_method'] == 'transfer' && !$myInventory['additional_supplier']) {
                $this->insertShipment($transaction_id, JWTAuth::user()->stockist_code);
            }

            DB::commit();

            $response = DB::table('transactions')->where('id', $transaction_id)->first();
            $response->detail = DB::table('transaction_details')->where('transaction_id',  $transaction_id)->get();
            $response->shipments = DB::table('transactions_shipments')->where('transaction_id', $transaction_id)->get();

            return $this->output([
                'status' => 1,
                'message' => 'Transaksi berhasil',
                'data' => $response,
            ]);
        } catch (QueryException $ex) {
            Log::error($ex->getMessage());
            DB::rollback();
            return $this->output([
                'status' => 0,
                'message' => $ex->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->output([
                'status' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function postEditStatus()
    {
        $postdata = request()->all();

        $type_status = ['diproses', 'dikirimkan', 'selesai', 'dibatalkan'];

        if (!in_array($postdata['status'], $type_status)) {
            return $this->output([
                'status' => 0,
                'message' => 'Terjadi kesalahan',
            ], '404');
        }

        $transaction = DB::table('transactions')->where('code', $postdata['code']);

        if ($transaction->count() == 0) {
            return $this->output([
                'status' => 0,
                'message' => 'Tidak terdapat data',
            ], '404');
        }

        $transaction = $transaction->first();
        $transaction->details = DB::table('transaction_details')->where('transaction_id', $transaction->id)->get();

        $process_status = $this->processStatus($transaction, $postdata['status']);
        if ($process_status['success'] == 0) {
            return $this->output([
                'status' => 0,
                'message' => $process_status['message']
            ]);
        }

        DB::table('transactions')->where('code', $postdata['code'])->update([
            'status' => $postdata['status']
        ]);

        return $this->output([
            'status' => 1,
            'message' => 'Success',
            'data' => DB::table('transactions')->where('code', $postdata['code'])->first(),
        ]);
    }

    private function processStatus($transaction, $status)
    {  
        $success = 1;
        $message = 'success';
        $supplier = DB::table('users')->where("code", $transaction->supplier_code)->first();

        switch ($status) {
            case 'diproses':
                // verification status before is "menunggu"

                if ($transaction->status == "menunggu") {
                    // decrement stock supplier
                    // $supplier = $this->checkSupplier($transaction->code);
    
    
                    // if not additional_supplier
                    // if (!$supplier['additional_supplier']) {
                    foreach ($transaction->details as $transaction_detail) {
                        $supplier_inventories_users = DB::table('inventories_users')
                                                        ->where('inventory_id', $transaction_detail->inventory_id)
                                                        ->where('user_id', $supplier->id);
    
                        $supplier_inventories_users->update([
                            'instock' => DB::raw('instock - '.$transaction_detail->qty),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    
                    }
                    // }  
                } else {
                    $success = 0;
                    $message = "Status harus menunggu untuk diproses";
                }
                
                break;
            case 'dikirimkan' :
                switch ($transaction->status) {
                    case 'menunggu':
                        // decrement suppliers inventories
                        foreach ($transaction['details'] as $transaction_detail) {
                            $supplier_inventories_users = DB::table('inventories_users')
                                                            ->where('inventory_id', $transaction_detail->inventory_id)
                                                            ->where('user_id', $supplier->id);
        
                            $supplier_inventories_users->update([
                                'instock' => DB::raw('instock - '.$transaction_detail->qty),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        
                        }
                    case 'diproses':
                        break;
                    default:
                        // print error
                        $success = 0;
                        $message = "Status harus menunggu atau diproses agar bisa dikirimkan";
                        break;
                }
                break;
            case 'selesai' :
                switch ($transaction->status) {
                    case 'menunggu':
                        // decrement suppliers inventories
                        foreach ($transaction->details as $transaction_detail) {
                            $supplier_inventories_users = DB::table('inventories_users')
                                                            ->where('inventory_id', $transaction_detail->inventory_id)
                                                            ->where('user_id', $supplier->id);
        
                            $supplier_inventories_users->update([
                                'instock' => DB::raw('instock - '.$transaction_detail->qty),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        
                        }
                    case 'diproses':
                    case 'dikirimkan':
                        // increment products buyer
                        foreach ($transaction->details as $transaction_detail) {
                            $my_inventories = DB::table('inventories_users')
                                                ->where('inventory_id', $transaction_detail->inventory_id)
                                                ->where('user_id', $transaction->user_id);
            
                            if ($my_inventories->count() > 0) {
                                $my_inventories->update([
                                    'instock' => DB::raw('instock + '.$transaction_detail->qty),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            } else {
                                $my_inventories->insert([
                                    'instock' => $transaction_detail->qty,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'user_id' => $transaction->user_id,
                                    'inventory_id' => $transaction_detail->inventory_id,
                                ]);
                            }
                        }
                        break;
                        
                    default:
                        // print error
                        $success = 0;
                        $message = "Status harus menunggu atau diproses agar bisa selesai";
                        break;
                }
                break;
            case 'dibatalkan' :
                // increment stock supplier if status before (diproses, dikirimkan, selesai)
                // $supplier = $this->checkSupplier($transaction->code);

                // if not additional_supplier
                // if (!$supplier['additional_supplier']) {
                    
                    // increment supplier
                    // verificartion if status before is (diproses, dikirimkan, selesai)
                    $increment_status = ['diproses', 'dikirimkan', 'selesai'];
                    if (in_array($status, $increment_status)) {
                        foreach ($transaction->details as $transaction_detail) {
                            DB::table('inventories_users')
                                ->where('inventory_id', $transaction_detail->inventory_id)
                                ->where('user_id', $supplier->id)
                                ->update([
                                    'instock' => DB::raw('instock + '.$transaction_detail->qty),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        // decrement buyer if before it "selesai"
                        if ($status == 'selesai') {
                            foreach ($transaction->details as $transaction_detail) {
                                DB::table('inventories_users')
                                    ->where('inventory_id', $transaction_detail->inventory_id)
                                    ->where('user_id', $transaction->user_id)
                                    ->update([
                                        'instock' => DB::raw('instock - '.$transaction_detail->qty),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }
                        }
                    } else {
                        $success = 0;
                        $message = 'Maaf, barang tidak bisa dibatalkan';
                    }
    
                // }

                break;
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function getRequestedProducts($id = null)
    {
        $transactions = DB::table('transactions')
                        ->where('supplier_code', JWTAuth::user()->code)
                        ->orderBy('created_at','desc');
        if ($id) {
            $transactions = $transactions->where('id', $id)->first();
            $transactions->details = DB::table('transaction_details')
                    ->leftJoin('inventories', 'transaction_details.inventory_id', '=', 'inventories.id')
                    ->where('transaction_id', $transactions->id)
                    ->select('transaction_details.*', DB::raw("CONCAT('".url("/storage")."/',sns_inventories.picts) as picts"), 'name')
                    ->get();
        } else {
            if ($transactions->count() == 0) {
                return $this->output([
                    'status' => 0,
                    'message' => 'Tidak terdapat data'
                ]);
            }
            $transactions = $transactions->paginate(request('paginate') ?? 15);
        }

        return $this->output([
            'status' => 1,
            'message' => 'success',
            'data' => $transactions,
        ]);
    }

    #ga jadi dipake
    private function checkSupplier($supplier_code = null)
    {
        $user_id = JWTAuth::user()->id;
        $additional_supplier = false;

        if (!empty($supplier_code)) {
            $supplier = DB::table('users')
                            ->where('code', $supplier_code)
                            ->whereNull('deleted_at');

            if ($supplier->count() > 0) {
                $user_id = $supplier->first()->id;

            } else {
                $supplier = DB::table('additional_suppliers')
                            ->where('code', $supplier_code);

                if ($supplier->count() == 0) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode([
                        'status' => 0,
                        'message' => 'Supplier tidak tersedia'
                    ]);
                    exit();

                } else {
                    $additional_supplier = true;
                }
            }
        }

        return [
            'inventories_user_id' => $user_id,
            'additional_supplier' => $additional_supplier,
        ];
    }

    # Done
    private function calculateTotal($postdata)
    {
        $total = 0;
        $inventory = [];
        $shipping_fee = $postdata['shipping_fee'] ?? 0;

        foreach ($postdata['inventory_id'] as $key => $inventory_id) {
            if ($postdata['type'] == "buy" && JWTAuth::user()->privilege_id == 3) {
                $supplier = DB::table('users')
                            ->where('code', JWTAuth::user()->stockist_code)
                            ->first();

                $supplier_stocks = DB::table('inventories_users')
                                    ->where('user_id', $supplier->id)
                                    ->where('inventory_id', $inventory_id)
                                    ->first();

                if ((($supplier_stocks->instock ?? 0) - ($supplier_stocks->outstock ?? 0)) < $postdata['inventory_qty'][$key]) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode([
                        'status' => 0,
                        'message' => 'Stok barang Stokis tidak mencukupi'
                    ]);
                    exit();
                }
            } elseif ($postdata['type'] == 'sale') {
                $my_stock = DB::table('inventories_users')
                                    ->where('user_id', JWTAuth::user()->id)
                                    ->where('inventory_id', $inventory_id)
                                    ->first();

                if ((($my_stock->instock ?? 0) - ($my_stock->outstock ?? 0)) < $postdata['inventory_qty'][$key]) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode([
                        'status' => 0,
                        'message' => 'Stok barang tidak mencukupi'
                    ]);
                    exit();
                }
            }

            $inventory = DB::table('inventories')->where('id', $inventory_id)->first();
            $price = $postdata['type'] == 'sale' ? $inventory->end_price : $inventory->member_price;


            $total = $total + ($price * $postdata['inventory_qty'][$key]);
        }

        $total = $total + $shipping_fee;

        return $total;
    }

    private function calculateDiscount($code_coupon = null, $total = 0)
    {
        $discount_percent = 0;
        $discount_price = 0;
        $discount = 0;

        if (!empty($code_coupon)) {
            $discount = DB::table('coupons')->where('code', $code_coupon);
            if ($discount->count() > 0) {
                $discount = $discount->first();
                if (isset($discount->percent) && $discount->percent != 0) {
                    $discount = $total * ($discount->percent / 100);
                    $discount_percent = $discount_percent;
                } elseif(isset($discount->price) && $discount->price != 0) {
                    $discount = $discount->price;
                    $discount_price = $discount->price;
                }   
            }
        }

        return [
            'grand_total' => $grand_total = $total - $discount,
            'percent' => $discount_percent,
            'price' => $discount_price,
            'total' => $discount,
        ];
    }

    private function insertTransaction($transaction, $postdata)
    {
        $transaction = array_merge($transaction, [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transaction_id = DB::table('transactions')->insertGetId($transaction);

        $this->insertTransactionDetail($transaction_id, $postdata);

        return $transaction_id;
    }

    private function insertTransactionDetail($transaction_id, $postdata)
    {
        $transaction_details = [];
        foreach ($postdata['inventory_id'] as $key => $inventory_id) {
            $inventory = DB::table('inventories')
                            ->where('id', $inventory_id)
                            ->first();
                
            $price = $postdata['type'] == 'sale' ? $inventory->end_price : $inventory->member_price;

            $transaction_details[] = [
                'transaction_id' => $transaction_id,
                'inventory_id' => $inventory_id,
                'price' => $price,
                'qty' => $postdata['inventory_qty'][$key],
                'subtotal' => $price * $postdata['inventory_qty'][$key],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            switch ($postdata['type']) {
                case 'buy':
                    // if ($postdata['payment_method'] == 'cod') {
                        // Update Inventory
                        // Update buyer inventory
                        // Jika Stockist langsung nambah
                        if (JWTAuth::user()->privilege_id == 2) {
                            $my_inventories = DB::table('inventories_users')
                                                ->where('inventory_id', $inventory_id)
                                                ->where('user_id', JWTAuth::user()->id);
    
                            if ($my_inventories->count() > 0) {
                                $my_inventories->update([
                                    'instock' => DB::raw('instock + '.$postdata['inventory_qty'][$key]),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            } else {
                                $my_inventories->insert([
                                    'instock' => $postdata['inventory_qty'][$key],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'user_id' => JWTAuth::user()->id,
                                    'inventory_id' => $inventory_id,
                                ]);
                            }
                        }

                        // Update Supplier Inventory
                        // if (JWTAuth::user()->privilege_id == 3) {
                        //     $supplier_stocks = DB::table('inventories_users')
                        //                                     ->where('inventory_id', $inventory_id)
                        //                                     ->where('user_id', JWTAuth::user()->stockist_code);

                        //     $supplier_stocks->update([
                        //         'outstock' => DB::raw('instock - '.$postdata['inventory_qty'][$key]),
                        //         'updated_at' => date('Y-m-d H:i:s'),
                        //     ]);
                        // }   
                    // }
                    break;
                case 'buy return':
                    break;
                case 'sale return':
                    break;
                default:
                    // default sale
                    DB::table('inventories_users')
                        ->where('inventory_id', $inventory_id)
                        ->where('user_id', JWTAuth::user()->id)
                        ->update([
                            'outstock' => DB::raw('outstock + '.$postdata['inventory_qty'][$key])
                        ]);
                    break;
            }
        }
        DB::table('transaction_details')->insert($transaction_details);
    }

    private function insertShipment($transaction_id, $supplier_code)
    {
        // insert origin
        $supplier_users = DB::table('users')
                ->where('code', JWTAuth::user()->stockist_code)
                ->first();
        $origin = DB::table("shipments")
                ->where('id', $supplier_users->id)
                ->select('province', 'province_id', 'city', 'city_id', 'subdistrict', 'subdistrict_id', 'address')
                ->first();
        $origin = array_merge($origin, [
            'transaction_id' => $transaction_id,
            'type' => 'origin',
        ]);

        // insert destination
        $destination = [
            'province' => request('province'),
            'province_id' => request('province_id'),
            'city' => request('city'),
            'city_id' => request('city_id'),
            'subdistrict' => request('subdistrict'),
            'subdistrict_id' => request('subdistrict_id'),
            'address' => request('address'),
            'type' => 'destination',
            'transaction_id' => $transaction_id,
        ];

        DB::table('transactions_shipments')->insert($origin, $destination);
    }
}
