<?php

use Illuminate\Database\Seeder;
use Energibangsa\Cepet\helpers\EB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EB::insert('users', [
            [
                'id' => 1,
                'name' => 'Admin',
                'privilege_id' => 1,
                'username' => 'admin',
                'password' => Hash::make('123456'),
                // 'created_by' => 1
            ],
            [
                'id' => 2,
                'name' => 'User',
                'privilege_id' => 2,
                'username' => 'user',
                'password' => Hash::make('123456'),
                // 'created_by' => 1
            ]
        ]);
    }
}
