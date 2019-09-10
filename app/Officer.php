<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Officer extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function hasRole($name)
    {
        if ($this->role->name == $name) return true;

        return false;
    }

    public function hasPermission($name)
    {
        $permission = $this->role->permissions()
            ->where('name', $name)
            ->first();

        if (is_null($permission)) return false;

        return true;
    }

    public function hasPermissions($names)
    {
        $permissions = $this->role->permissions()
            ->whereIn('name', $names)
            ->get();

        if ($permissions->count() < 1) return false;

        return true;
    }

    public function regions()
    {
        return $this->belongsToMany('\App\Region', 'region_officer');
    }

    public function role()
    {
        return $this->belongsTo('\App\Role');
    }

    public function officerTokens() {
        return $this->hasMany('\App\UserToken', 'user_id');
    }
}
