<?php

namespace App\Validators;

use App\V1\CustomerClient;

class CustomerValidator {
    public function validateCustomerInClient($attribute, $value, $parameters, $validator)
    {
        $customer = CustomerClient::where('client', $parameters[0])
            ->where('client_id', $value)
            ->first();

        if (is_null($customer)) return false;

        return true;
    }

    public function messageCustomerInClient($message, $attribute, $rule, $parameters) {
        return 'Your customer not found.';
    }
}