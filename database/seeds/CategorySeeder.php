<?php

use Illuminate\Database\Seeder;
use Energibangsa\Cepet\helpers\EB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EB::insert('categories', [

            [
                'id' => 1,
                'name' => 'Primer'
            ],
            [
                'id' => 2,
                'name' => 'Sekunder'
            ],
            [
                'id' => 3,
                'name' => 'Tersier'
            ]
        ]);
    }
}
