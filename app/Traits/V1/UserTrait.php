<?php

namespace App\Traits\V1;

trait UserTrait {
    public function user()
    {
        if (auth('api')->check()) {
            return auth('api')->user();
        } else if (auth('officer')->check()) {
            return auth('officer')->user();
        } else if (auth('web')->check()) {
            return auth('web')->user();
        } else {
            return null;
        }
    }
}
