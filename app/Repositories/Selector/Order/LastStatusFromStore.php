<?php

namespace App\Repositories\Selector\Order;

class LastStatusFromStore {
    public function apply($model, $storeId)
    {
        return $model->selectRaw(
            "(SELECT status FROM store_order_histories WHERE store_id={$storeId} AND order_id=orders.id ORDER BY created_at DESC LIMIT 1) as status"
        );
    }
}
