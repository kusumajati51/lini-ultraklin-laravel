<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function officers()
    {
        return $this->belongsToMany('\App\Officer', 'region_officer');
    }

    public function packages()
    {
        return $this->hasMany('\App\Package');
    }

    public function banners()
    {
        return $this->hasMany('\App\Banner');
    }

    public function promotions()
    {
        return $this->hasMany('\App\Promotion');
    }

    public function customers()
    {
        return $this->hasMany('\App\Customer');
    }

    public function orders()
    {
        return $this->hasMany('\App\Order');
    }
}
