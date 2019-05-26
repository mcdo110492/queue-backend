<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Roles
         * 1- Admin
         * 2- Counter
         * 3- Ticket Issuer
         */

        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'administrator@email.com',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'name' => 'Jane Doe',
            'email' => 'janedoe@email.com',
            'username' => 'janedoe',
            'password' => Hash::make('janedoe'),
            'role' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'name' => 'John Doe',
            'email' => 'johndoe@email.com',
            'username' => 'johndoe',
            'password' => Hash::make('johndoe'),
            'role' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}