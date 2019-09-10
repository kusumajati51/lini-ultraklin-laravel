<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function parent() {
        return $this->belongsTo('App\Service', 'parent_id', 'id');
    }
    
    public function child() {
        return $this->hasMany('App\Service', 'parent_id', 'id');
    }

    public function packages()
    {
        return $this->hasMany('App\Package');
    }
}
