<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'created_by', 'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function generateAgentCode()
    {
        $levels = [
            config('ultraklin_const.LEVEL_AGENT'),
            config('ultraklin_const.LEVEL_SALES')
        ];

        $prefix = substr($this->name, 0, 2);
        $counter = User::select('code')
            ->whereIn('status', $levels)
            ->count();

        $no = str_pad($counter + 1, 4, 0, STR_PAD_LEFT);

        return strtoupper($prefix).$no;
    }

    // Scope
    public function scopeByRegion($query)
    {
        return $query->where(function ($query) {
            $query->whereHas('orders', function ($order) {
                $regionIds = auth('officer')->user()->regions->pluck('id');
    
                $order->whereIn('region_id', $regionIds);
            });

            if (auth('officer')->user()->hasRole('admin')) {
                $query->orDoesntHave('orders');
            }
        });
    }

    // Relation
    public function firebaseTokens() {
        return $this->hasMany('App\UserToken');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function levels()
    {
        return $this->belongsToMany(
            'App\V1\Level',
            'user_level',
            'user_id',
            'level_id'
        )->withPivot([
            'active',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ]);
    }

    public function orders()
    {
        return $this->hasManyThrough(
            'App\V1\Order',
            'App\V1\Invoice',
            'user_id',
            'invoice_id',
            'id',
            'id'
        );
    }

    public function passwordResetHistories()
    {
        return $this->hasMany('App\PasswordResetHistory');
    }

    public function promotions()
    {
        return $this->belongsToMany(
            'App\V1\Promotion',
            'user_promotion_histories',
            'user_id',
            'promotion_id'
        )
        ->withPivot('date', 'code', 'total_discount')
        ->withTimestamps();
    }

    public function promotionHistories()
    {
        return $this->hasMany('App\V1\UserPromotionHistory');
    }

    public function store()
    {
        return $this->hasOne('App\V1\UserStore');
    }

    public function userTokens() {
        return $this->hasMany('App\UserToken');
    }
}
