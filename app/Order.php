<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $casts = [
        'detail' => 'array',
    ];

    protected $appends = [
        'total_quantity', 'additional_cso'
    ];

    // Attributes
    public function getDetailAttribute($value)
    {
        return json_decode($value);
    }

    public function getTotalQuantityAttribute()
    {
        return (int) $this->items()->sum('quantity');
    }

    public function getAdditionalCSOAttribute()
    {
        if (isset($this->detail->total_cso) && $this->detail->total_cso > 1) {
            return (int) $this->detail->total_cso - 1;
        }

        return 0;
    }

    public function getTotalPriceItemAttribute()
    {
        $total = collect($this->items)
            ->sum(function ($item) {
                return $item->pivot->price * $item->pivot->quantity;
            });

        return (int) $total;
    }

    public function getTotalPriceAdditionalCSOAttribute()
    {
        return (int) $this->totalPriceItem * $this->additionalCSO;
    }

    public function getDiscountAttribute() {
        $discount = 0;

        if ($this->hasPromotion()) {
            $discount = $this->invoice->discount;
        }

        return $discount;
    }

    public function getTotalPriceAttribute()
    {
        $this->total_discount = $this->discount;

        return (int) $this->totalPriceItem + $this->totalPriceAdditionalCSO - $this->total_discount;
    }

    // Helper
    public static function generateCode($offline = false)
    {
        $prefix = $offline ? 'UK-OFF' : 'UK-ONL';
        $code = date('Ymd').strtoupper(str_random(5));

        return $prefix.$code;
    }

    public function humanKey($key)
    {
        $str = str_replace('_', ' ', $key);

        return strtoupper($str);
    }

    public function hasPromotion()
    {
        if (!is_null($this->invoice->promotion)) {
            $orders = $this->invoice->orders
                ->whereIn('package_id', $this->invoice->promotion->packages()->pluck('id'));
            
            $order = $orders->where('total_price_item', $orders->max('total_price_item'))->first();

            if (is_null($order)) return false;

            if ($this->id == $order->id) return true;
        }

        return false;
    }

    public function isOfflineOrder()
    {
        if ($this->invoice->user()->exists()) {
            return false;
        }

        return true;
    }

    public function getPromotion() {
        $invoice = $this->invoice;

        if (!$invoice->has('promotion')) {
            return null;
        }

        $order = $invoice->getOrderWithPromotion();

        if (is_null($order) && $order->id != $this->id) {
            return null;
        }

        return $this->invoice->promotion;
    }

    public function getPrice()
    {
        $this->total_price_item = $this->total_price_item;
        $this->total_price_additional_cso = $this->total_price_additional_cso;
        $this->total_price = $this->total_price;
    }

    // Scope
    public function scopeByRegion($query)
    {
        return $query->whereIn('region_id', auth('officer')->user()->regions->pluck('id'));
    }

    // Relation
    public function region()
    {
        return $this->belongsTo('\App\Region');
    }
    
    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function items()
    {
        return $this->belongsToMany('App\Item', 'order_items')
            ->withPivot('price', 'quantity', 'package');
    }

    
}
