<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordResetHistory extends Model
{
    protected $casts = [
        'data' => 'array'
    ];
    
    protected $fillable = ['user_id', 'data'];
}
