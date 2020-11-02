<?php

use Illuminate\Database\Seeder;
use Energibangsa\Cepet\helpers\EB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EB::insert('settings', [
            [
                'name' => 'App Name',
                'value' => 'Cepet'
            ],
            [
                'name' => 'App Descriptions',
                'value' => 'Cepet'
            ]
        ]);
    }
}
