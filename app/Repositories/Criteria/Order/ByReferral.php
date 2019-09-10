<?php

namespace App\Repositories\Criteria\Order;

class ByReferral {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        $model = $model->where(function ($query) {
            $query->whereHas('invoice', function ($invoice) {
                $invoice->whereHas('user', function ($user) {
                    $user->select('id', 'name')
                        ->where('referral', $this->filter['referral']);
                });
            });
        });

        return $model;
    }
}
