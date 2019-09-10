<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $casts = [
        'attributes' => 'array'
    ];

    public function parent() {
        return $this->belongsTo('App\Service', 'parent_id', 'id');
    }
    
    public function child() {
        return $this->hasMany('App\Service', 'parent_id', 'id');
    }

    public function packages()
    {
        return $this->hasMany('App\V1\Package');
    }
}
