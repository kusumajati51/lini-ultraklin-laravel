<?php

namespace App\Traits\V1;

use App\Utils\Util;

trait OrderModelTrait {
    public function visibleNumberFormat()
    {
        $this->format_number = (object) [
            'subtotal' => $this->sub_total,
            'extra_cso' => $this->extra_price_cso,
            'discount' => $this->discount,
            'final_total' => $this->final_total 
        ];
    }

    public function visibleCurrencyFormat()
    {
        $this->format_currency = (object) [
            'subtotal' => Util::idrCurrency($this->sub_total),
            'extra_cso' => Util::idrCurrency($this->extra_price_cso),
            'discount' => Util::idrCurrency($this->discount),
            'final_total' => Util::idrCurrency($this->final_total) 
        ];
    }

    public function visibleFormat()
    {
        $this->visibleNumberFormat();
        $this->visibleCurrencyFormat();
    }
}
