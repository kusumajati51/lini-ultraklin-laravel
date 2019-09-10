<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Promotion extends Model
{
    protected $casts = [
        'day' => 'array',
        'time' => 'array'
    ];

    protected $appends = [
        'daily', 'time_start', 'time_end'
    ];

    /*---------- ATTRIBUTE ----------*/
    public function getDailyAttribute()
    {
        $time = $this->time;

        $isHourly = preg_match('/\d{2}:\d{2}/', json_encode($time));

        if ($isHourly) return true;

        return false;
    }

    public function getTimeStartAttribute()
    {
        $time = $this->time;

        return Carbon::parse($time[0])->timestamp;
    }

    public function getTimeEndAttribute()
    {
        $time = $this->time;

        return Carbon::parse($time[1])->timestamp;
    }

    public function getTargetNameAttribute()
    {
        $targets = collect(config('ultraklin.promotion_targets'));

        $target = $targets->where('name', $this->target)->first();

        return $target['display_name'];
    }

    /*---------- REALATION ----------*/
    public function packages()
    {
        return $this->belongsToMany(
            'App\V1\Package',
            'package_promotion',
            'promotion_id',
            'package_id'
        );
    }
}
