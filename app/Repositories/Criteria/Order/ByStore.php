<?php

namespace App\Repositories\Criteria\Order;

class ByStore {
    public function apply($model, $storeId)
    {
        $model = $model->whereRaw(
            "orders.id in (SELECT order_id FROM store_order_histories WHERE store_id = '{$storeId}')"
        );

        return $model;
    }
}
