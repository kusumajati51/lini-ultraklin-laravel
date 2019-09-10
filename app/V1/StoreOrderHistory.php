<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class StoreOrderHistory extends Model
{
    protected $fillable = [
        'store_id', 'order_id', 'status'
    ];
}
