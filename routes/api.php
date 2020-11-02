<?php

use Illuminate\Http\Request;
use Energibangsa\Cepet\helpers\EB;
use Illuminate\Support\Facades\Route;
use App\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

EB::routeController('auth','API\AuthAPIController');
EB::routeController('open', 'API\OpenAPIController');
Route::middleware('jwt.verify')->group(function () {
    EB::routeController('profiles', 'API\ProfilesAPIController');
    // EB::routeController('inventories', 'API\InventoriesAPIController');
    // EB::routeController('categories', 'API\CategoriesAPIController');
    // EB::routeController('transactions', 'API\TransactionsAPIController');
    // EB::routeController('suppliers', 'API\SuppliersAPIController');
    // EB::routeController('subscriptions', 'API\SubscriptionsAPIController');
    // EB::routeController('points', 'API\PointsAPIController');
    EB::routeController('calons', 'Api\CalonsAPIController');
    EB::routeController('details', 'Api\PolingDetailsAPIController');
    EB::routeController('polings', 'Api\PolingsAPIController');
    EB::routeController('insert', 'API\InsertSuaraAPIController');
    Route::resource('detail', 'API\DetailAPIController');
    Route::resource('tps', 'API\TPSAPIController');
    Route::resource('count', 'API\CountAPIController');
    Route::GET('tpsName', 'API\TPSAPIController@tpsName');
});