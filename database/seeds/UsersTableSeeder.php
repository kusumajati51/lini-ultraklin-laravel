<?php

use Illuminate\Database\Seeder;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = 'Ata';
        $user->phone = '0816904883';
        $user->email = 'ata@ultraklin.com';
        $user->password = bcrypt(123456);
        $user->status = 'tester';
        $user->save();
    }
}
