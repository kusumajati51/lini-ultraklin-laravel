<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Setting;

class SettingController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $settings = Setting::orderBy('name')->get();

        return view('admin.setting.index', compact(
            'settings'
        ));
    }

    public function ajaxUpdate()
    {
        foreach ($this->request->settings as $key => $val) {
            $setting = Setting::where('name', $key)->first();

            if ($setting->input_type == 'datepicker' && $setting->data_type == 'array') {
                
                if (is_null($val)) {
                    $setting->value = json_encode([]);
                    $setting->save();

                    continue;
                }
                
                $start = Carbon::createFromFormat('Y-m-d', $val[0]);
                $end = Carbon::createFromFormat('Y-m-d', $val[1]);

                $setting->value = json_encode([
                    $start->toDateString(),
                    $end->toDateString()
                ]);

                $setting->updated_by = auth('officer')->user()->name;

                $setting->save();
            }
        }

        $settings = Setting::orderBy('name')->get();
        
        return response()->json([
            'success' => 1,
            'message' => 'Setting updated.',
            'data' => $settings
        ]);
    }
}
