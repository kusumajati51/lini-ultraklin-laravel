<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

use App\Utils\Util;

use App\Traits\V1\ModelTrait;
use App\Traits\V1\OrderModelTrait;

class Order extends Model
{
    use ModelTrait, OrderModelTrait;

    protected $casts = [
        'detail' => 'array',
    ];

    /*---------- HELPER ----------*/
    public static function generateCode($offline = false)
    {
        $prefix = $offline ? 'UK-OFF' : 'UK-ONL';
        $code = date('Ymd').strtoupper(str_random(5));

        return $prefix.$code;
    }

    public function getPromotion()
    {
        if ($this->hasPromotion()) {
            return $this->invoice->promotion;
        }

        return null;
    }

    public function isOnline()
    {
        if ($this->invoice->user) {
            return true;
        } else if ($this->invoice->customer->client) {
            return true;
        }

        return false;
    }

    public function hasDiscount()
    {
        if ($this->discount > 0) return true;

        return false;
    }

    public function hasPromotion()
    {
        if (is_null($this->invoice->promotion)) return false;

        $promotion = $this->invoice->promotion;

        $promotionPackages =  $promotion->packages()
            ->select('id')
            ->pluck('id')
            ->toArray();

        $orders = $this->invoice->orders()
            ->select('id', 'invoice_id', 'package_id')
            ->get();

        $orders = collect($orders)->filter(function ($order) use ($promotionPackages) {
            return in_array($order->package_id, $promotionPackages);
        })
        ->map(function ($order) {
            $items = $order->items()->get();

            $order->total = collect($items)->sum(function ($item) {
                return $item->pivot->price * $item->pivot->quantity;
            });

            return $order;
        });

        $orderWithPromotion = $orders->where('total', $orders->max('total'))
            ->first();

        if (is_null($orderWithPromotion) || $orderWithPromotion->id != $this->id) return false;

        return true;
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
    public function getUserAttribute()
    {
        if ($this->invoice->user) {
            return $this->invoice->user;
        } else {
            return $this->invoice->customer;
        }
    }

    public function getDetailAttribute($value)
    {
        $value = json_decode($value, true);

        $hasTotalCSO = isset($value['total_cso']);

        $value['additional_cso'] =  $hasTotalCSO ? ($value['total_cso'] - 1) : 0;

        return $value;
    }

    public function getPromotionAttribute()
    {
        return $this->invoice->promotion;
    }

    public function getAdditionalCsoAttribute()
    {
        $totalCS0 = 0;

        if (isset($this->detail['total_cso']) && $this->detail['total_cso'] > 1) {
            $totalCS0 = $this->detail['total_cso'] - 1;
        }

        return (int) $totalCS0;
    }

    public function getExtraPriceCsoAttribute()
    {
        return (int) $this->total * $this->additional_cso;
    }

    public function getSubTotalAttribute()
    {
        return (int) $this->total + $this->extra_price_cso;
    }

    public function getFinalTotalAttribute()
    {
        return (int) $this->total + $this->extra_price_cso - $this->discount;
    }

    public function getNumberAttribute()
    {
        return (object) [
            'total' => $this->total,
            'extra_price_cso' => $this->extra_price_cso,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'final_total' => $this->final_total
        ];
    }

    public function getCurrencyAttribute()
    {
        $util = new Util;

        return (object) [
            'total' => $util->humanPrice($this->total),
            'extra_price_cso' => $util->humanPrice($this->extra_price_cso),
            'sub_total' => $util->humanPrice($this->sub_total),
            'discount' => $util->humanPrice($this->discount),
            'final_total' => $util->humanPrice($this->final_total)
        ];
    }

    public function getPricesAttribute()
    {
        return (object) [
            'total' => $this->total,
            'extra_price_cso' => $this->extra_price_cso,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'final_total' => $this->final_total
        ];
    }

    public function getHumanPricesAttribute()
    {
        $util = new Util;

        return (object) [
            'total' => $util->humanPrice($this->total),
            'extra_price_cso' => $util->humanPrice($this->extra_price_cso),
            'sub_total' => $util->humanPrice($this->sub_total),
            'discount' => $util->humanPrice($this->discount),
            'final_total' => $util->humanPrice($this->final_total)
        ];
    }

    /*---------- SCOPE ----------*/
    public function scopeByRegion($query)
    {
        return $query->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
    }

    /*---------- RELATION ----------*/
    public function invoice()
    {
        return $this->belongsTo('App\V1\Invoice');
    }

    public function items()
    {
        return $this->belongsToMany('App\V1\Item', 'order_items')
            ->withPivot('price', 'quantity', 'package');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function region()
    {
        return $this->belongsTo('\App\Region');
    }

    public function store()
    {
        return $this->belongsTo('\App\V1\UserStore');
    }

    public function ratings()
    {
        return $this->hasOne('App\V1\Rating');
    }

}
