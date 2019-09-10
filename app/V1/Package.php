<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';

    /*---------- SCOPE ----------*/
    public function scopeByRegion($query)
    {
        return $query->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
    }

    /*---------- REALATION ----------*/
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
        return $this->belongsToMany(
            'App\V1\Promotion',
            'package_promotion',
            'package_id',
            'promotion_id'
        );
    }
}
