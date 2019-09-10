<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';

    public function packages()
    {
        return $this->belongsToMany('App\Package', 'package_item')
            ->withPivot('price');
    }
}
