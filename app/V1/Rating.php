<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public function orders()
    {
        return $this->belongsTo('App\V1\Order');
    }

    public function invoices()
    {
        return $this->belongsTo('App\V1\invoices');
    }
}
