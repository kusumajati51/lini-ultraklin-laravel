<?php

namespace App\Validators;

use App\Item;

class ItemValidator {
    public function validateItemInPackage($attribute, $value, $parameters, $validator)
    {
        $item = Item::whereHas('packages', function ($package) use ($parameters) {
            $package->where('name', $parameters[0]);
        })
        ->find($value);

        if (is_null($item)) return false;

        return true;
    }

    public function messageItemInPackage($message, $attribute, $rule, $parameters) {
        return 'Item not found in selected package.';
    }
}