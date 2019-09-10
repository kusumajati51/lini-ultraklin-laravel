<?php

namespace App\Utils;

use App\Utils\TimeUtil;

class FilterUtil {
    protected $defaultFilters;
    protected $filters;

    protected $timeUtil;

    public function __construct()
    {
        $this->timeUtil = new TimeUtil;

        $this->defaultFilters = [
            'invoice' => [
                'limit' => 25,
                'sort' => ['invoices.id', 'desc'],
                'date' => [
                    $this->timeUtil->createTimeRange(date('Y-m-d'))->start->toDateTimeString(),
                    $this->timeUtil->createTimeRange(date('Y-m-d'))->end->toDateTimeString()
                ],
                'status' => ['Paid', 'Unpaid'],
                'user_status' => ['user'],
                'online' => 1,
                'region' => [],
                'codes' => []
            ],
            'order' => [
                'limit' => 25,
                'ref_code' => '',
                'region' => [],
                'time' => [
                    $this->timeUtil->createTimeRange(date('Y-m-d'))->start->toDateTimeString(),
                    $this->timeUtil->createTimeRange(date('Y-m-d'))->end->toDateTimeString()
                ],
                'status' => ['Pending', 'Cancel', 'Confirm', 'On The Way', 'Process', 'Done'],
                'service' => 'all',
                'order_source' => ['Online'],
                'user_status' => ['user'],
                'payment_status' => ['Paid', 'Unpaid'],
                'sort' => ['orders.id', 'desc'],
                'search' => ''
            ],
            'user' => [
                'limit' => 25,
                'sort' => ['users.id', 'desc'],
                'ref_code' => '',
                'min_order' => 0,
                'region' => [],
                'status' => ['user'],
                'time' => [
                    $this->timeUtil->createTimeRange(date('Y-m-d'))->start->toDateTimeString(),
                    $this->timeUtil->createTimeRange(date('Y-m-d'))->end->toDateTimeString()
                ],
                'search' => ''
            ]
        ];
    }

    public function getDefaultFilter($name = null)
    {
        if (is_null($name) && !isset($this->defaultFilters[$name])) return;

        return $this->defaultFilters[$name];
    }

    public function setFilter($name = null, $data = [])
    {
        if (is_null($name) && !isset($this->defaultFilters[$name])) return;

        $this->filter[$name] = [];

        foreach ($data as $key => $val) {
           if (isset($this->defaultFilters[$name][$key])) {
               $this->filter[$name][$key] = $val; 
           }
        }

        return $this;
    }

    public function getFilter($name = null)
    {
        if (is_null($name) && !isset($this->filter[$name])) return [];

        return $this->filter[$name];
    }
}
