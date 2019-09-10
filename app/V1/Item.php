<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

use App\Traits\V1\ModelTrait;

class Item extends Model
{
    use ModelTrait;

    protected $table = 'items';

    /*---------- HELPER ----------*/
    public function visiblePrices()
    {
        $this->prices = $this->prices;
    }

    public function visibleHumanPrices()
    {
        $this->human_prices = $this->human_prices;
    }

    /*---------- ATTRIBUTE ----------*/
    public function getPricesAttribute()
    {
        return (object) [
            'price' => $this->pivot->price,
            'sub_total' => $this->pivot->price * $this->pivot->quantity,
            'total' => $this->pivot->price * $this->pivot->quantity
        ];
    }

    public function getHumanPricesAttribute()
    {
        return (object) [
            'price' => human_price($this->pivot->price),
            'sub_total' => human_price($this->pivot->price * $this->pivot->quantity),
            'total' => human_price($this->pivot->price * $this->pivot->quantity)
        ];
    }

    /*---------- RELATION ----------*/
    public function packages()
    {
        return $this->belongsToMany('App\V1\Package', 'package_item')
            ->withPivot('price');
    }
}
