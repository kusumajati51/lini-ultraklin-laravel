<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $appends = ['group'];

    public function getGroupAttribute()
    {
        $splitedName = explode('__', $this->name);

        return $splitedName[0];
    }
}
