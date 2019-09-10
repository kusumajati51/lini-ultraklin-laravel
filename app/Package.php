<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';

    // Scope
    public function scopeByRegion($query)
    {
        return $query->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
    }

    public function region()
    {
        return $this->belongsTo('\App\Region');
    }

    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function items()
    {
        return $this->belongsToMany('App\Item', 'package_item')
            ->withPivot('price');
    }

    public function promotions()
    {
        return $this->hasMany('App\Promotion');
    }
}
