<?php

use Illuminate\Database\Seeder;
use Energibangsa\Cepet\helpers\EB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EB::insert('permissions', [
            [
                'privilege_id' => '1',
                'menu_id' => '1',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
            ],
            [
                'privilege_id' => '1',
                'menu_id' => 2,
                'browse' => 1,
                'read' => '0',
                'edit' => '0',
                'add' => '0',
                'delete' => '0',
            ],
            [
                'privilege_id' => '1',
                'menu_id' => '3',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
            ],
            [
                'privilege_id' => '1',
                'menu_id' => '4',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
            ],
            [
                'privilege_id' => '1',
                'menu_id' => '5',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
            ],
            [
                'privilege_id' => '1',
                'menu_id' => 6,
                'browse' => 1,
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
            ],
            [
                'privilege_id' => '1',
                'menu_id' => '7',
                'browse' => '1',
                'read' => '1',
                'edit' => '1',
                'add' => '1',
                'delete' => '1',
            ]
        ]);
    }
}
