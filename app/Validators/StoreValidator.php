<?php

namespace App\Validators;

use App\User;
use App\V1\UserStore;

class StoreValidator {
    public function validateCanCreateStore($attribute, $value, $parameters, $validator)
    {
        $user = User::find($value);

        if ($user->status != 'user' && $user->status != 'tester') return false;

        $store = UserStore::where('user_id', $value)->first();

        return is_null($store);
    }

    public function messageCanCreateStore($message, $attribute, $rule, $parameters)
    {
        return 'Oops! Sorry you can\'t create store.';
    }
}
