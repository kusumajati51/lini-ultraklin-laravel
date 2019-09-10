<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function hasPermission($name)
    {
        $permission = $this->permissions()
            ->where('name', $name)
            ->first();

        if (is_null($permission)) return false;

        return true;
    }

    public function hasPermissions($names)
    {
        $permissions = $this->permissions()
            ->whereIn('name', $names)
            ->get();

        if ($permissions->count() < 1) return false;

        return true;
    }

    public function officers()
    {
        return $this->hasMany('App\Officer');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            'App\Permission',
            'role_permission',
            'role_id',
            'permission_id'
        );
    }
}
