<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public function getValueAttribute($value) {
        if ($this->data_type == 'array') {
            return json_decode($value, true);
        }

        return $value;
    }
}
