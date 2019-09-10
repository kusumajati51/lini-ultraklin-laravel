<?php

namespace App\Utils;

class Util
{
    public function humanPrice($price)
    {
        return 'Rp. '.number_format($price, 0, ',', '.');
    }

    public static function idrCurrency($number)
    {
        return 'Rp. '.number_format($number, 0, ',', '.');
    }
}
