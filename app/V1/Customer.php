<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

/**
 * App\V1\Customer
 *
 * @property int $id
 * @property int|null $region_id
 * @property string $name
 * @property string|null $email
 * @property string $phone
 * @property string $status
 * @property string|null $info
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\V1\CustomerClient $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $invoices
 * @property-read \App\Region|null $region
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer byRegion()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\Customer whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Customer extends Model
{
    /*---------- SCOPE ----------*/
    public function scopeByRegion($query)
    {
        return $query->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
    }

    /*---------- REALATION ----------*/
    public function region()
    {
        return $this->belongsTo('\App\Region');
    }
    
    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function client()
    {
        return $this->hasOne('App\V1\CustomerClient');
    }

    public function promotionHistories()
    {
        return $this->hasMany('App\V1\UserPromotionHistory');
    }

    public function promotions()
    {
        return $this->belongsToMany(
            'App\V1\Promotion',
            'user_promotion_histories',
            'customer_id',
            'promotion_id'
        )
        ->withPivot('date', 'code', 'total_discount')
        ->withTimestamps();
    }
}
