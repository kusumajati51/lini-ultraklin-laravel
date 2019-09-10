<?php

namespace App\Repositories;

use App\Repositories\Repository;

class OrderRepository extends Repository {
    public function model()
    {
        return '\App\V1\Order';
    }

    public function defaultFilter()
    {
        $timeUtil = (new \App\Utils\TimeUtil)->createTimeRange(date('Y-m-d'));

        return [
            'limit' => 25,
            'referral' => '',
            'region' => [],
            'time' => [
                $timeUtil->start->toDateTimeString(),
                $timeUtil->end->toDateTimeString()
            ],
            'status' => ['Pending', 'Cancel', 'Confirm', 'On The Way', 'Process', 'Done'],
            'service' => 'all',
            'order_source' => ['Online'],
            'user_status' => ['user'],
            'payment_status' => ['Paid', 'Unpaid'],
            'sort' => ['orders.id', 'desc'],
            'search' => ''
        ];
    }

    public function filterCriteria()
    {
        return [
            'referral' => '\App\Repositories\Criteria\Order\ByReferral',
            'region' => '\App\Repositories\Criteria\Order\InRegion',
            'time' => '\App\Repositories\Criteria\Order\InTimeRange',
            'status' => '\App\Repositories\Criteria\Order\InStatus',
            'service' => '\App\Repositories\Criteria\Order\ByService',
            'order_source' => '\App\Repositories\Criteria\Order\InOrderSource',
            'user_status' => '\App\Repositories\Criteria\Order\InUserStatus',
            'payment_status' => '\App\Repositories\Criteria\Order\InPaymentStatus',
            'search' => '\App\Repositories\Criteria\Order\BySearch'
        ];
    }

    public function collect()
    {
        $this->model = $this->model->select([
            'orders.*',
            'packages.display_name as package_display_name',
            'regions.code as region_code', 'regions.name as region_name',
            'users.name as user_name', 'users.phone as user_phone', 'users.status as user_status'
        ]);

        $this->model->leftJoin('packages', 'packages.id', 'orders.package_id');
        $this->model->leftJoin('invoices', 'invoices.id', 'orders.invoice_id');
        $this->model->leftJoin('regions', 'regions.id', 'orders.region_id');
        $this->model->leftJoin('users', 'users.id', 'invoices.user_id');

        $this->model->with([
            'invoice',
            'items'
        ]);

        return $this;
    }
}
