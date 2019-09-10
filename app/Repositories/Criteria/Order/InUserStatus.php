<?php

namespace App\Repositories\Criteria\Order;

class InUserStatus {
    protected $filter;

    public function apply($model, $filter)
    {
        $this->filter = $filter;

        $userStatus = array_filter($this->filter['user_status'], function ($status) {
            return in_array(strtolower($status), ['user', 'tester']);
        });

        $toUserStatus = array_filter($this->filter['user_status'], function ($status) {
            return in_array(strtolower($status), ['sales', 'agent', 'partner']);
        });

        $model = $model->where(function ($order) use ($userStatus, $toUserStatus) {
            $order->whereHas('invoice', function ($invoice) use ($userStatus) {
                $invoice->whereHas('user', function ($user) use ($userStatus) {
                    $user->whereIn('status', $userStatus);
                })
                ->orWhereHas('customer', function ($customer) use ($userStatus) {
                    $customer->whereIn('status', $userStatus);
                });
            });

            if (in_array('agent', $toUserStatus)) {
                $order->orWhereHas('invoice', function ($invoice) use ($toUserStatus) {
                    $query = "select * from users where users.id = invoices.user_id and exists (select * from users as u where u.status = 'agent' and u.code = users.referral)";

                    $invoice->whereRaw("exists ({$query})");
                });
            }

            if (in_array('sales', $toUserStatus)) {
                $order->orWhereHas('invoice', function ($invoice) use ($toUserStatus) {
                    $query = "select * from users where users.id = invoices.user_id and exists (select * from users as u where u.status = 'sales' and u.code = users.referral)";

                    $invoice->whereRaw("exists ({$query})");
                });
            }

            if (in_array('partner', $toUserStatus)) {
                $order->orWhereHas('store');
            }
        });

        return $model;
    }
}
