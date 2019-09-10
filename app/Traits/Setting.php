<?php

namespace App\Traits;

use Carbon\Carbon;

trait Setting {
    public function checkServiceClosed($date = null)
    {
        // Check service is closed
        $setting = \App\Setting::where('name', 'close_service')->first();

        if (!is_null($setting)) {
            if (count($setting->value) > 0) {
                $start = Carbon::createFromFormat('Y-m-d', $setting->value[0]);
                $end = Carbon::createFromFormat('Y-m-d', $setting->value[1]);
    
                $start->setTimeFromTimeString(config('ultraklin.time.working_time')[0]);
                $end->setTimeFromTimeString(config('ultraklin.time.working_time')[1]);
                

                // For older app
                if (is_null($date)) {
                    foreach ($this->request->orders as $order) {
                        $item = json_decode($order);
                        $date = Carbon::createFromFormat('Y-m-d H:i', $item->date);
    
                        if ($start->diffInMinutes($date, false) >= 0 && $end->diffInMinutes($date, false) <= 0) {
                            return ([
                                'error' => 'Sorry, our service closed from '.$start->toDateString().' to '.$end->toDateString().'.'
                            ]);
                        }
                    }
                }
                else {
                    if ($start->diffInMinutes($date, false) >= 0 && $end->diffInMinutes($date, false) <= 0) {
                        return ([
                            'error' => 'Sorry, our service closed from '.$start->toDateString().' to '.$end->toDateString().'.'
                        ]);
                    }
                }

            }
        }
    }
}