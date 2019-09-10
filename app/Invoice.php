<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class Invoice extends Model
{
    protected $appends = [
        'total',
        'order_with_promotion',
        'invoice_rating',
        // 'ref_code'
    ];

    public function getTotalAttribute()
    {
        $total = collect($this->items)
            ->sum(function ($item) {
                return $item->price * $item->quantity;
            });

        $totalAdditionalCSO = collect($this->orders)
            ->sum(function ($order) {
                return $order->additional_cso;
            });

        $total = $total + ($total * $totalAdditionalCSO);

        return $total;
    }

    public function getOrderWithPromotionAttribute()
    {
        if (is_null($this->promotion)) return [];

        // Get max of total from order filterd by promo
        $orders = $this->orders
            ->whereIn('package_id', $this->promotion->packages()->pluck('id'));

        $order = $orders->where('total_price_item', $orders->max('total_price_item'))->first();

        return $order;
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
    //     return $this->user->referral;
    // }

    public static function generateCode()
    {
        $totalOfMonth = DB::table('invoices')
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $prefix = 'UK-INV';
        $no = STR_PAD($totalOfMonth + 1, 4, 0, STR_PAD_LEFT);
        $code = $prefix.date('Ymd').'-'.$no;

        return $code;
    }

    public function getOrderWithPromotion()
    {
        if (is_null($this->promotion)) return [];

        // Get max of total from order filterd by promo
        $orders = $this->orders->filter(function ($order) {
            return $order->package->name == $this->promotion->package->name;
        });

        $order = $orders->where('total_price_item', $orders->max('total_price_item'))->first();

        return $order;
    }

    public function scopeByRegion($query)
    {
        return $query->whereHas('orders', function ($order) {
            $order->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
        });
    }

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
