<?php

namespace App\Utils;

use Carbon\Carbon;

class TimeUtil
{
    public function createDateInMonthRange($yearMonth = null)
    {
        if (is_null($yearMonth)) {
            $yearMonth = date('Y-m');
        } else {
            $yearMonth = date('Y-m', strtotime($yearMonth));
        }

        $start = Carbon::parse($yearMonth)->startOfMonth()->subDay(1)
            ->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]);
        $end = Carbon::parse($yearMonth)->endOfMonth()
            ->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1]);

        return (object) [
            'start' => $start,
            'end' => $end
        ];
    }

    public function createTimeRange($startDate = null, $endDate = null)
    {
        if (is_null($startDate)) {
            $startDate = date('Y-m-d');
        } else {
            $startDate = date('Y-m-d', strtotime($startDate));
        }

        if (is_null($endDate)) {
            $endDate = date('Y-m-d');
        } else {
            $endDate = date('Y-m-d', strtotime($endDate));
        }

        $start = Carbon::parse($startDate)->subDay(1)
            ->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]);
        $end = Carbon::parse($endDate)
            ->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1]);

        return (object) [
            'start' => $start,
            'end' => $end
        ];
    }
}
