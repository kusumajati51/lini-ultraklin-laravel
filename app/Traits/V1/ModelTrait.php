<?php

namespace App\Traits\V1;

trait ModelTrait {
    public function extraAttributes($attributes = null)
    {
        if (is_string($attributes)) {
            $this->{$attributes} = $this->getAttribute($attributes);
        } else if (is_array($attributes)) {
            foreach ($attributes as $attribute) {
                $this->{$attribute} = $this->getAttribute($attribute);
            }
        }
    }
}