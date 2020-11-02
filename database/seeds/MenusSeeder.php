<?php

use Illuminate\Database\Seeder;
use Energibangsa\Cepet\helpers\EB;

class MenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EB::insert('menus', [
            [
                'id' => 1,
                'up_id' => null,
                'title' => 'Dashboard',
                'orderable' => 1,
                'icon' => 'media/svg/icons/Design/Layers.svg',
                'page' => 'admin/dashboard'
            ],
            [
                'id' => 2,
                'up_id' => null,
                'title' => 'Settings',
                'orderable' => 2,
                'icon' => 'flaticon-home',
                'page' => null
            ],
            [
                'id' => 3,
                'up_id' => 2,
                'title' => 'General Settings',
                'orderable' => 1,
                'icon' => null,
                'page' => 'admin/settings/settings',
            ],
            [
                'id' => 4,
                'up_id' => 2,
                'title' => 'Privileges',
                'orderable' => 2,
                'icon' => null,
                'page' => 'admin/settings/privileges',
            ],
            [
                'id' => 5,
                'up_id' => 2,
                'title' => 'Menus',
                'orderable' => 3,
                'icon' => null,
                'page' => 'admin/settings/menus',
            ],
            [
                'id' => 6,
                'up_id' => 2,
                'title' => 'Users',
                'orderable' => 4,
                'icon' => null,
                'page' => 'admin/settings/users',
            ],
            [
                'id' => 7,
                'up_id' => 2,
                'title' => 'Permissions',
                'orderable' => 5,
                'icon' => null,
                'page' => 'admin/settings/permissions',
            ],
            
        ]);
    }
}
