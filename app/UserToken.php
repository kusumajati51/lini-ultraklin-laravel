<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = 'user_tokens';
    protected $fillable = ['user_id', 'token', 'type', 'active'];

    public function users() {
        return $this->belongsTo('App\User');
    }

    public function officers() {
        return $this->belongsTo('App\Officer');
    }
}
