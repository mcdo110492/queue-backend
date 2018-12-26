<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $address = ['street' => "Street", 'city' => "City", 'province' => 'Province', 'zip' => '6110', 'country' => 'Philippines'];
        $phone_number = ['telephone' => '(03) 451 223', 'mobile_number' => '(+63) 9723 1111'];

        DB::table('settings_application')->insert([
            'company_name' => 'Company Name',
            'address' => json_encode($address),
            'phone_number' => json_encode($phone_number),
            'company_color' => 'red',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
