<?php

use Illuminate\Database\Seeder;

use App\Setting;

class PatchSeeder__Setting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'name' => 'close_service',
                'display_name' => 'Close a Service',
                'value' => json_encode([]),
                'data_type' => 'array',
                'input_type' => 'datepicker'
            ]
        ];

        foreach ($settings as $setting) {
            $_setting = Setting::where('name', $setting['name'])->first();

            if (is_null($_setting)) {
                Setting::create($setting);
            }
        }
    }
}
