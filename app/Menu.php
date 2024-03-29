<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    public function items()
    {
        return $this->hasMany('App\MenuItem');
    }
}
