<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function invoices() {
        return $this->belongsTo('App\V1\Invoice');
    }
}
