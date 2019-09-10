<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

class UserStore extends Model
{
    protected $fillable = [
        'user_id',
        'region_id',
        'code',
        'name',
        'phone',
        'email',
        'owner',
        'identity_card_number',
        'identity_card',
        'address',
        'lat',
        'lng',
        'description',
        'active',
        'created_by',
        'updated_by'
    ];

    protected static function generateCode($name)
    {
        $prefix = substr($name, 0, 2);
        $counter = UserStore::select('code')
            ->count();

        $no = str_pad($counter + 1, 4, 0, STR_PAD_LEFT);

        return strtoupper($prefix).$no;
    }

    public function images()
    {
        return $this->hasMany('\App\V1\StoreImage', 'store_id', 'id');
    }

    public function orderHistories()
    {
        return $this->hasMany('\App\V1\StoreOrderHistory', 'store_id', 'id');
    }

    public function packages()
    {
        return $this->belongsToMany(
            '\App\Package',
            'store_package',
            'store_id',
            'package_id'
        );
    }

    public function region()
    {
        return $this->belongsTo('\App\Region');
    }

    public function user()
    {
        return $this->belongsTo('\App\User');
    }
}
