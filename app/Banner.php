<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Storage;

class Banner extends Model
{
    protected function getFileAttribute($value) {
        if (!Storage::exists('banners/'.$value)) {
            return 'default.png';
        }

        return $value;
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
}
