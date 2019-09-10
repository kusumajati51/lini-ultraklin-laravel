<?php

use Illuminate\Database\Seeder;

use App\Officer;
use App\Role;

class OfficersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csRole = Role::where('name', 'customer-service')->first();

        $cs = new Officer;
        $cs->name = 'Customer Service';
        $cs->phone = '1111111111';
        $cs->gender = 'Male';
        $cs->email = 'cs@ultraklin.com';
        $cs->password = bcrypt(123456);

        $csRole->officers()->save($cs);
    }
}
