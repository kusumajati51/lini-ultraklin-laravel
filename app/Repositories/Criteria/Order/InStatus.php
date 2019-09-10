<?php

namespace App\Repositories\Criteria\Order;

class InStatus {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        return $model->whereIn('orders.status', $this->filter['status']);
    }
}
