<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

use App\Utils\Util;

class Invoice extends Model
{
    protected $appends = [
        'invoice_rating',
        // 'ref_code'
    ];

    /*---------- HELPER ----------*/
    public static function generateCode()
    {
        $totalOfMonth = Invoice::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $prefix = 'UK-INV';
        $no = STR_PAD($totalOfMonth + 1, 4, 0, STR_PAD_LEFT);
        $code = $prefix.date('Ymd').'-'.$no;

        return $code;
    }

    public function getUser()
    {
        return is_null($this->customer_id) ? $this->user : $this->customer;
    }

    public function visibleNumber()
    {
        $this->number = $this->number;
    }

    public function visibleCurrency()
    {
        $this->currency = $this->currency;
    }

    public function visiblePrices()
    {
        $this->prices = $this->prices;
    }

    public function visibleHumanPrices()
    {
        $this->human_prices = $this->human_prices;
    }

    /*---------- ATTRIBUTE ----------*/
    public function getPaymentAttribute($value)
    {
        if (is_null($this->payments) || is_null($this->payments->bank)) return $value;

        return $this->payments->bank;
    }

    public function getFinalTotalAttribute()
    {
        return (int) ($this->total - $this->canceled_amount) - ($this->discount - $this->canceled_discount);
    }

    public function getNumberAttribute()
    {
        return [
            'total' => $this->total,
            'discount' => $this->discount,
            'final_total' => $this->final_total
        ];
    }

    public function getCurrencyAttribute()
    {
        $util = new Util;

        return [
            'total' => $util->humanPrice($this->total),
            'discount' => $util->humanPrice($this->discount),
            'final_total' => $util->humanPrice($this->final_total)
        ];
    }

    public function getPricesAttribute()
    {
        return [
            'total' => $this->total,
            'discount' => $this->discount,
            'final_total' => $this->final_total
        ];
    }

    public function getHumanPricesAttribute()
    {
        $util = new Util;

        return [
            'total' => $util->humanPrice($this->total),
            'discount' => $util->humanPrice($this->discount),
            'final_total' => $util->humanPrice($this->final_total)
        ];
    }

    public function getInvoiceRatingAttribute()
    {
        $rate = 0;

        $ratings = $this->ratings();

        if ($ratings->count() == 0) {
            return null;
        }

        foreach ($ratings->get() as $rating) {
            $rate = $rate + $rating->votes;
        }

        $rate = $rate / $ratings->count();

        return $rate;
    }

    // public function getRefCodeAttribute()
    // {
    //     if ($this->invoices) {
    //         # code...
    //     }
    //     return $this->user->referral;
    // }

    /*---------- SCOPE ----------*/
    public function scopeByRegion($query)
    {
        return $query->whereHas('orders', function ($order) {
            $order->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
        });
    }

    /*---------- REALATION ----------*/
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function customer()
    {
        return $this->belongsTo('App\V1\Customer');
    }

    public function promotion()
    {
        return $this->belongsTo('App\V1\Promotion');
    }

    public function orders()
    {
        return $this->hasMany('App\V1\Order');
    }

    public function regions()
    {
        return $this->belongsToMany(
            'App\Region',
            'orders',
            'invoice_id',
            'region_id'  
        );
    }

    public function items()
    {
        return $this->hasManyThrough(
            'App\OrderItem',
            'App\V1\Order',
            'invoice_id',
            'order_id',
            'id',
            'id'
        );
    }

    public function payments() {
        return $this->hasOne('App\Payment');
    }

    public function ratings()
    {
        return $this->hasMany('App\V1\Rating');
    }
}
