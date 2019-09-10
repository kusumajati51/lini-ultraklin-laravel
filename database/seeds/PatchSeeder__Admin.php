<?php

use Illuminate\Database\Seeder;

use App\Officer;
use App\Permission;
use App\Role;

class PatchSeeder_Admin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsSeeder::class);

        $permissions = Permission::all()->pluck('id');

        $role = Role::where('name', 'admin')->first();

        $role->permissions()->attach($permissions);

        $this->command->info('Permissions attached to Admin role');

        $admin = new Officer;
        $admin->name = 'Admin';
        $admin->phone = '1111111111';
        $admin->gender = 'Male';
        $admin->email = 'admin@ultraklin.com';
        $admin->password = bcrypt('ultra1324');

        $admin->role()->associate($role);

        $admin->save();

        $this->command->info('User ('.$admin->email.') was created');
    }
}
