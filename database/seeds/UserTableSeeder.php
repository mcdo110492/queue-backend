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
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 1,
            'department_id' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

    }
}