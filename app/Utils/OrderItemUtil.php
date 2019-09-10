<?php

namespace App\Utils;

use App\Utils\Util;

class OrderItemUtil extends Util
{
    public function getPrices($item)
    {
        return [
            'price' => $item->pivot->price,
            'sub_total' => $item->pivot->price * $item->pivot->quantity
        ];
    }

    public function getHumanPrices($item)
    {
        return [
            'price' => $this->humanPrice($item->pivot->price),
            'sub_total' => $this->humanPrice($item->pivot->price * $item->pivot->quantity)
        ];
    }
}
