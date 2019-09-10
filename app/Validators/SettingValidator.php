<?php

namespace App\Validators;

use Carbon\Carbon;

class SettingValidator {
    public function validateOpenAt($attribute, $value, $parameters, $validator)
    {
        $setting = \App\Setting::where('name', 'close_service')->first();

        if (!is_null($setting) && !is_null($parameters[0])) {
            if (count($setting->value) > 0) {
                $date = Carbon::createFromFormat('Y-m-d H:i',$parameters[0]);
                $start = Carbon::createFromFormat('Y-m-d', $setting->value[0]);
                $end = Carbon::createFromFormat('Y-m-d', $setting->value[1]);
    
                $start->setTimeFromTimeString(config('ultraklin.time.working_time')[0]);
                $end->setTimeFromTimeString(config('ultraklin.time.working_time')[1]);
                
                if ($date->diffInMinutes($start, false) <= 0 && $date->diffInMinutes($end, false) >= 0) {
                    return false;
                }
            }
        }

        return true;
    }

    public function messageOpenAt($message, $attribute, $rule, $parameters) {
        $setting = \App\Setting::where('name', 'close_service')->first();

        if (is_null($setting) || count($setting->value) == 2) {
            return 'Sorry, our service closed from '.$setting->value[0].' to '.$setting->value[1].'.';    
        }

        return 'Sorry, our service closed.';
    }

    public function validateCanOrder($attribute, $value, $parameters, $validator)
    {
        $dateString = date('Y-m-d', strtotime($parameters[0]));

        $date = Carbon::createFromFormat('Y-m-d H:i', $parameters[0]);
        $start = Carbon::createFromFormat('Y-m-d H:i', $dateString.' '.config('ultraklin.time.working_time')[0]);
        $end = Carbon::createFromFormat('Y-m-d H:i', $dateString.' '.config('ultraklin.time.working_time')[1]);
        
        if ($date->diffInMinutes($start, false) <= 0 && $date->diffInMinutes($end, false) >= 0) {
            return true;
        }

        return false;
    }

    public function messageCanOrder($message, $attribute, $rule, $parameters) {
        $start = Carbon::createFromFormat('H:i', config('ultraklin.time.working_time')[0]);
        $end = Carbon::createFromFormat('H:i', config('ultraklin.time.working_time')[1]);

        return 'Sorry, our service open at '.$start->toTimeString().' - '.$end->toTimeString().'.';
    }
}