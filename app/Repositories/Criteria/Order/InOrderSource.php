<?php

namespace App\Repositories\Criteria\Order;

class InOrderSource {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        $isOnline = in_array('Online', $filter['order_source']);
        $isOffine = in_array('Offline', $filter['order_source']);

        $model = $model->where(function ($order) use ($isOnline, $isOffine) {
            $order->whereHas('invoice', function ($invoice) use ($isOnline, $isOffine) {
                if ($isOnline && $isOffine) {
                    $invoice->whereHas('user')->orWhereHas('customer');
                } else if ($isOnline) {
                    $invoice->whereHas('user')
                        ->orWhereHas('customer', function ($customer) {
                            $customer->whereHas('client');
                        });
                } else if ($isOffine) {
                    $invoice->whereHas('customer', function ($customer) {
                        $customer->doesntHave('client');
                    });
                }
            });
        });

        return $model;
    }
}
