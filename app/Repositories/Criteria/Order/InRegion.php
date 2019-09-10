<?php

namespace App\Repositories\Criteria\Order;

class InRegion {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        if (count($this->filter['region']) > 0) {
            $model = $model->whereHas('region', function ($region) {
                $region->whereIn('code', $this->filter['region']);
            });
        }

        return $model;
    }
}
