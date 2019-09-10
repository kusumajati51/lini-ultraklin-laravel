<?php

namespace App;

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

    public function getDailyAttribute()
    {
        $time = $this->time;

        $timeStart = Carbon::parse($time[0]);
        $timeEnd = Carbon::parse($time[1]);

        if (is_null($this->day) && $timeStart->diffInDays($timeEnd) > 0) {
            return false;
        }

        return true;
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

    // Scope
    public function scopeByRegion($query)
    {
        return $query->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
    }

    // Relation
    public function region()
    {
        return $this->belongsTo('\App\Region');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }
}
