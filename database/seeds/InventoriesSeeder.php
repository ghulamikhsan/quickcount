<?php

use Illuminate\Database\Seeder;
use Energibangsa\Cepet\helpers\EB;

class InventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EB::insert('inventories', [
            [
                'name'=> 'a',
                'code'=> '1234',
                'picts'=> 'https://s2.bukalapak.com/img/7218896632/large/Bedak_Wardah_Refill_Lightening_Two_Way_Cake_Light_Feel.jpg',
                'descriptions'=> "Hai haiwjadopjaesfag ini bagus lohh",
                'category_id'=> 1
            ],
            [
                'name'=> 'b',
                'code'=> '1234',
                'picts'=> 'https://s2.bukalapak.com/img/7218896632/large/Bedak_Wardah_Refill_Lightening_Two_Way_Cake_Light_Feel.jpg',
                'descriptions'=> "Hai haiwjadopjaesfag ini bagus lohh",
                'category_id'=> 2
            ],
            [
                'name'=> 'c',
                'code'=> '1234',
                'picts'=> 'https://s2.bukalapak.com/img/7218896632/large/Bedak_Wardah_Refill_Lightening_Two_Way_Cake_Light_Feel.jpg',
                'descriptions'=> "Hai haiwjadopjaesfag ini bagus lohh",
                'category_id'=> 3
            ],
            [
                'name'=> 'd',
                'code'=> '1234',
                'picts'=> 'https://s2.bukalapak.com/img/7218896632/large/Bedak_Wardah_Refill_Lightening_Two_Way_Cake_Light_Feel.jpg',
                'descriptions'=> "Hai haiwjadopjaesfag ini bagus lohh",
                'category_id'=> 1
            ],
            [
                'name'=> 'e',
                'code'=> '1234',
                'picts'=> 'https://s2.bukalapak.com/img/7218896632/large/Bedak_Wardah_Refill_Lightening_Two_Way_Cake_Light_Feel.jpg',
                'descriptions'=> "Hai haiwjadopjaesfag ini bagus lohh",
                'category_id'=> 2
            ],
            [
                'name'=> 'f',
                'code'=> '1234',
                'picts'=> 'https://s2.bukalapak.com/img/7218896632/large/Bedak_Wardah_Refill_Lightening_Two_Way_Cake_Light_Feel.jpg',
                'descriptions'=> "Hai haiwjadopjaesfag ini bagus lohh",
                'category_id'=> 3
            ],
        ]);
    }
}
