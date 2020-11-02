<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Energibangsa\Cepet\helpers\EB;

use Auth;
use Request;
use DB;

class AuthController extends Controller
{

    public function getIndex() {
        if (Auth::check() || Auth::viaRemember()) {
            return redirect()->intended('admin/dashboard');
        }

        return view('auth.login');
    }

    public function postLogin() {
        $credentials = Request::only('username', 'password');
        $credentials = array_merge($credentials, ['deleted_at' => null]);
        $remember = !empty(Request::input('remember')) ? TRUE : FALSE;

        if (Auth::attempt($credentials, $remember)) {
            // Authentication passed...
            $acess_menu = [];
            
            $myMenuId = DB::table('permissions')
                                ->where('privilege_id', Auth::user()->privilege_id)
                                ->select('menu_id')
                                ->get()
                                ->toArray();
            $myMenuId = array_column($myMenuId, 'menu_id');
            
            $menus = DB::table('menus')->whereNotNull('page')->get()->toArray();
            $myMenu = DB::table('menus')->whereIn('id', $myMenuId)->whereNotNull('page')->get()->toArray();
            $myMenu = array_column($myMenu, 'page');
            $notMyMenu = DB::table('menus')->whereNotIn('id', $myMenuId)->whereNotNull('page')->get()->toArray();
            $notMyMenu = array_column($notMyMenu, 'page');

            $permissions = DB::table('menus')
                                ->RightJoin('permissions', 'menus.id', '=', 'permissions.menu_id')
                                ->where('permissions.privilege_id', Auth::user()->privilege_id)
                                ->select('menus.*', 'browse', 'read', 'edit', 'add', 'delete', 'trash')
                                ->orderBy('up_id', 'asc')
                                ->orderBy('orderable', 'asc')
                                ->get();

            foreach ($permissions as $permission) {
                if ($permission->section == 1) {
                    $access_menus['menu'.$permission->id] = [ 'section' => $permission->title];
                } elseif ($permission->up_id == null) {
                    $access_menus['menu'.$permission->id] = [
                        'title' => $permission->title,
                        'root' => true,
                        'icon' => $permission->icon,
                        'page' => $permission->page,
                        'new-tab' => $permission->new_tab == 1 ? true : false,
                    ];
                } else {
                    if(!isset($access_menus['menu'.$permission->up_id])) {
                        $key = array_search($permission->up_id, array_column($menus, 'id'));

                        $access_menus['menu'.$permission->up_id] = [
                            'title' => $menus[$key]->title,
                            'root' => true,
                            'icon' => $menus[$key]->icon,
                            'bullet' => 'dot',
                            'submenu' => [
                                [
                                    'title' => $permission->title,
                                    'page' => $permission->page,
                                    'new_tab' => ($permission->new_tab == 1 ? true : false),
                                ],
                            ],
                        ];
                    } else {
                        $access_menus['menu'.$permission->up_id]['bullet'] = 'dot';
                        $access_menus['menu'.$permission->up_id]['submenu'][] = [
                            'title' => $permission->title,
                            'page' => $permission->page,
                            'new_tab' => $permission->new_tab == 1 ? true : false,
                        ];
                    }
                }
            }

            Request::session()->put('permissions', $permissions);
            Request::session()->put('menus', $access_menus);
            Request::session()->put('access', [
                'access' => $myMenu,
                'forbidden' => $notMyMenu,
            ]);

            $res['status']  = 1;
            $res['message'] = 'Berhasil Login';
        }else{
            $res['status']  = 0;
            $res['message'] = 'Username & Password tidak sesuai. Coba Lagi.';
        }

        return response()->json($res);
    }

    public function getLogout()
    {
        // proses logout
        Auth::logout();
        Request::session()->forget(['permissions', 'menus', 'access']);

        return redirect()->intended('admin/auth');
    }
}
