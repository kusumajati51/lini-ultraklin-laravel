<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
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
    
    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }
}
