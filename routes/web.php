<?php

use Illuminate\Support\Facades\Route;
use Energibangsa\Cepet\helpers\EB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
    return redirect()->intended('admin/dashboard');
});

Route::get('storage/{file1}/{file2?}/{file3?}', function ($file1, $file2 = null, $file3 = null)
{
    $filename = $file1 . ($file2 ? "/$file2" : '') . ($file3 ? "/$file3" : '');
    $path = storage_path('app/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('report_point/{code}', 'API\PointsAPIController@getReports');
Route::get('products/{code}', 'Master\InventoriesController@views');
EB::routeController('/home', 'HomeController');

Route::prefix('admin')->group(function() {
    EB::routeController('auth', 'AuthController');
    Route::middleware(['auth', 'checkAccess'])->group(function() {
        Route::prefix('settings')->group(function() {
            EB::routeController('settings', 'Settings\SettingController');
            EB::routeController('privileges', 'Settings\PrivilegeController');
            EB::routeController('users', 'Settings\UserController');
            EB::routeController('menus', 'Settings\MenuController');
            EB::routeController('permissions', 'Settings\PermissionController');
        });
        EB::routeController('dashboard', 'DashboardController');
        EB::routeController('calons', 'CalonController');
        EB::routeController('polings', 'PolingController');
        EB::routeController('details', 'DetailController');
        EB::routeController('reports', 'ReportController');
        EB::routeController('counts', 'CountsController');
        EB::routeController('index', 'DashboardTableController');
        Route::get('tables', 'DashboardController@dashboardTable');
        Route::resource('table', 'TableController');
        Route::resource('chart', 'ChartController');
    });
});


// Demo routes
Route::get('/widget', 'PagesController@index');
Route::get('/datatables', 'PagesController@datatables');
Route::get('/ktdatatables', 'PagesController@ktDatatables');
Route::get('/select2', 'PagesController@select2');
Route::get('/icons/custom-icons', 'PagesController@customIcons');
Route::get('/icons/flaticon', 'PagesController@flaticon');
Route::get('/icons/fontawesome', 'PagesController@fontawesome');
Route::get('/icons/lineawesome', 'PagesController@lineawesome');
Route::get('/icons/socicons', 'PagesController@socicons');
Route::get('/icons/svg', 'PagesController@svg');

// Quick search dummy route to display html elements in search dropdown (header search)
Route::get('/quick-search', 'PagesController@quickSearch')->name('quick-search');
