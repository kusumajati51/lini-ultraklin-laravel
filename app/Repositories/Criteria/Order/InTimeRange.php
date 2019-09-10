<?php

namespace App\Repositories\Criteria\Order;

class InTimeRange {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        return $model->whereBetween('orders.created_at', $this->filter['time']);
    }
}
