<?php

use Illuminate\Database\Seeder;
use Energibangsa\Cepet\helpers\EB;

class PrivilegesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EB::insert('privileges', [
            'id' => 1,
            'name' => 'Admin'
        ]);
        EB::insert('privileges', [
            'id' => 2,
            'name' => 'User'
        ]);
    }
}
