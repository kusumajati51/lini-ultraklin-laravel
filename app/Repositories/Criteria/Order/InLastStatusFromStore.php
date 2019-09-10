<?php

namespace App\Repositories\Criteria\Order;

class InLastStatusFromStore {
    protected $filter;

    public function apply($model, $data)
    {
        $statusString = implode("', '", $data['status']);

        return $model->whereRaw(
            "(SELECT COUNT(*) FROM store_order_histories WHERE store_id={$data['store_id']} AND order_id=orders.id AND status IN ('{$statusString}')) > 0"
        );
    }
}
