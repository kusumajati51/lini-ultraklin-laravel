<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);

        $this->call(UsersTableSeeder::class);
        
        $this->call(OfficersTableSeeder::class);
        
        $this->call(ServiceDataSeeder::class);

        $this->call(PriceSeeder::class);

        $this->call(RestoreDataSeeder::class);
    }
}
