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
            'priority_ticket_color' => 'red',
            'normal_ticket_color' => 'blue',
            'time_format' => '12 Hours',
            'date_format' => 'mm dd yyyy',
            'display_mode' => 'Display Mode 1'
        ];

        DB::table('settings_display')->insert([
            'display_settings' => json_encode($data),
            'created_at' => $now,
            'updated_at' => $now
        ]);


    }
}
