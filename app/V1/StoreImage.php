<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class StoreImage extends Model
{
    protected $fillable = [
        'store_id',
        'filename',
        'created_by',
        'updated_by'
    ];
}
