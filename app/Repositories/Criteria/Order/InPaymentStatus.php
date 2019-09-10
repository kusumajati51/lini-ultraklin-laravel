<?php

namespace App\Repositories\Criteria\Order;

class InPaymentStatus {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        return $model->whereHas('invoice', function ($invoice) {
            $invoice->whereIn('status', $this->filter['payment_status']);
        });
    }
}
