<?php

namespace App\Repositories\Criteria\Order;

class BySearch {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        $model = $model->where(function ($query) {
            $query->where('orders.code', 'like', '%'.$this->filter['search'].'%')
                ->orWhere('orders.location', 'like', '%'.$this->filter['search'].'%')
                ->orWhere('orders.note', 'like', '%'.$this->filter['search'].'%')
                ->orWhere('orders.detail', 'like', '%'.$this->filter['search'].'%')
                ->orWhereHas('invoice', function ($invoice) {
                    $invoice->where('code', 'like', '%'.$this->filter['search'].'%')
                        ->orWhereHas('user', function ($user) {
                            $user->where('name', 'like', '%'.$this->filter['search'].'%')
                                ->orWhere('email', 'like', '%'.$this->filter['search'].'%')
                                ->orWhere('phone', 'like', '%'.$this->filter['search'].'%');
                        })
                        ->orWhereHas('customer', function ($customer) {
                            $customer->where('name', 'like', '%'.$this->filter['search'].'%')
                                ->orWhere('email', 'like', '%'.$this->filter['search'].'%')
                                ->orWhere('phone', 'like', '%'.$this->filter['search'].'%');
                        });
                });
        });

        return $model;
    }
}
