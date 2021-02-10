<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->email = 'emilioaor@gmail.com';
        $user->name = 'Emilio Ochoa';
        $user->password = bcrypt('123456');
        $user->role = User::ROLE_ADMIN;
        $user->save();

        $user = new User();
        $user->email = 'jlzreik@yezzcorp.com';
        $user->name = 'Jose Luis Zreik';
        $user->password = bcrypt('123456');
        $user->role = User::ROLE_ADMIN;
        $user->save();

        if (config('app.env') !== 'production') {
            $user = new User();
            $user->email = 'inventory@mail.com';
            $user->name = 'User Inventory';
            $user->password = bcrypt('123456');
            $user->role = User::ROLE_INVENTORY_MANAGER;
            $user->save();

            $user = new User();
            $user->email = 'warehouse@mail.com';
            $user->name = 'User Warehouse';
            $user->password = bcrypt('123456');
            $user->role = User::ROLE_WAREHOUSE;
            $user->save();
        }
    }
}
