<?php

namespace App\Traits\V1;

trait OfficerTrait {
    protected function officer()
    {
        return $this->request->user('officer');
    }
}
