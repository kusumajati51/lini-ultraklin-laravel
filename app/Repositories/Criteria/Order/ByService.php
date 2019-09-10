<?php

namespace App\Repositories\Criteria\Order;

class ByService {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        return $model->whereHas('package', function ($package) {
            $package->whereHas('service', function ($service) {
                if (strtolower($this->filter['service']) != 'all') {
                    $service->where('name', $this->filter['service']);
                }
            });
        });
    }
}
