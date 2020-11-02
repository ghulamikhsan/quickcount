<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsSeeder::class);
        $this->call(PrivilegesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(MenusSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(InventoriesSeeder::class);
    }
}
