<?php

use Illuminate\Database\Seeder;
use illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsDisplaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $data = [
            'display_mode' => 'Display Mode 1',
            'slider_type' => 'image'
        ];

        DB::table('settings_display')->insert([
            'display_settings' => json_encode($data),
            'created_at' => $now,
            'updated_at' => $now
        ]);


    }
}