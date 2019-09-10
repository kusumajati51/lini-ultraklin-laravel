<?php

namespace App\Utils;

use Carbon\Carbon;

use App\V1\Order;

use App\Interfaces\V1\CollectionInterface;

use App\Utils\Util;
use App\Utils\FilterUtil;

class OrderUtil implements CollectionInterface {
    protected $defaultFilter, $filter, $orders;

    public function __construct()
    {
        $this->defaultFilter = (new FilterUtil)->getDefaultFilter('order');
    }

    public function model()
    {
        return $this->orders;
    }

    public function collect($fields = '*')
    {
        $this->orders = Order::select($fields);

        return $this;
    }

    public function get()
    {
        if (!isset($this->filter['sort'])) {
            $this->filter['sort'] = $this->defaultFilter['sort'];
        }

        return $this->orders
            ->orderBy($this->filter['sort'][0], $this->filter['sort'][1])
            ->get();
    }

    public function paginate()
    {
        if (!isset($this->filter['limit'])) {
            $this->filter['limit'] = $this->defaultFilter['limit'];
        }

        if (!isset($this->filter['sort'])) {
            $this->filter['sort'] = $this->defaultFilter['sort'];
        }

        return $this->model()
            ->orderBy($this->filter['sort'][0], $this->filter['sort'][1])
            ->paginate($this->filter['limit']);
    }

    /**
     * Filter
     */
    public function getDefaultFilter($except = []) {
        $filter = collect($this->defaultFilter)->except($except)->all();

        return $filter;
    }

    public function setFilter($filter = [])
    {
        $this->filter = array_merge($this->defaultFilter, $filter);

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function filter($requestFilter = [], $except = [])
    {
        $this->filter = [];

        $defaultFilter = $this->getDefaultFilter($except);
        $filter = collect($defaultFilter)->merge($requestFilter)->all();

        if (!isset($filter['limit'])) {
            $this->filter['limit'] = $this->defaultFilter['limit'];
        }

        if (!isset($filter['sort'])) {
            $this->filter['sort'] = $this->defaultFilter['sort'];
        }

        if (isset($filter['time']) && count($filter['time']) == 2) {
            $time = (new \App\Utils\TimeUtil)->createTimeRange($filter['time'][0], $filter['time'][1]);

            $filter['time'] = [
                $time->start->toDateTimeString(),
                $time->end->toDateTimeString()
            ];
        }

        foreach ($filter as $key => $val) {
            if (!isset($this->defaultFilter[$key])) continue;

            $this->filter[$key] = is_null($val) ? $this->defaultFilter[$key] : $val;

            if (isset($this->filterMethods()[$key])) {
                $this->{$this->filterMethods()[$key]}();
            }
        }

        return $this;
    }

    public function filterMethods()
    {
        return [
            'ref_code' => 'filterByUpline',
            'region' => 'filterByRegion',
            'time' => 'filterByTime',
            'status' => 'filterByStatus',
            'service' => 'filterByService',
            'order_source' => 'filterByOrderSource',
            'payment_status' => 'filterByPaymentStatus',
            'user_status' => 'filterByUserStatus',
            'search' => 'search'
        ];
    }

    public function filterByUpline()
    {
        $this->model()->where(function ($query) {
            $query->whereHas('invoice', function ($invoice) {
                $invoice->whereHas('user', function ($user) {
                    $user->select('id', 'name')
                        ->where('referral', $this->filter['ref_code']);
                });
            });
        });

        return $this;
    }

    public function filterByRegion()
    {
        if (count($this->filter['region']) > 0) {
            $this->model()->whereHas('region', function ($region) {
                $region->whereIn('code', $this->filter['region']);
            });
        }

        return $this;
    }

    public function filterByService()
    {
        $this->model()->whereHas('package', function ($package) {
            $package->whereHas('service', function ($service) {
                if (strtolower($this->filter['service']) != 'all') {
                    $service->where('name', $this->filter['service']);
                }
            });
        });
        return $this;
    }

    public function filterByTime()
    {
        $this->model()->whereBetween('orders.created_at', $this->filter['time']);

        return $this;
    }

    public function filterByStatus()
    {
        $this->model()->whereIn('orders.status', $this->filter['status']);

        return $this;
    }

    public function filterByPaymentStatus()
    {
        $this->model()->whereHas('invoice', function ($invoice) {
            $invoice->whereIn('status', $this->filter['payment_status']);
        });

        return $this;
    }

    public function filterByUserStatus()
    {
        $userStatus = array_filter($this->filter['user_status'], function ($status) {
            return in_array(strtolower($status), ['user', 'tester']);
        });

        $toUserStatus = array_filter($this->filter['user_status'], function ($status) {
            return in_array(strtolower($status), ['sales', 'agent', 'partner']);
        });

        $this->model()->where(function ($order) use ($userStatus, $toUserStatus) {
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

        return $this;
    }

    public function filterByOrderSource()
    {
        $isOnline = in_array('Online', $this->filter['order_source']);
        $isOffine = in_array('Offline', $this->filter['order_source']);

        $this->model()->where(function ($order) use ($isOnline, $isOffine) {
            $order->whereHas('invoice', function ($invoice) use ($isOnline, $isOffine) {
                if ($isOnline && $isOffine) {
                    $invoice->whereHas('user')->orWhereHas('customer');
                } else if ($isOnline) {
                    $invoice->whereHas('user')
                    ->orWhereHas('customer', function ($customer) {
                        $customer->whereHas('client');
                    });
                } else if ($isOffine) {
                    $invoice->whereHas('customer', function ($customer) {
                        $customer->doesntHave('client');
                    });
                }
            });
        });

        return $this;
    }

    public function useMainFilter()
    {
        $this->filterByRegion()
            ->filterByTime()
            ->filterByStatus()
            ->filterByPaymentStatus()
            ->filterByUserStatus()
            ->filterByOrderSource();

        return $this;
    }

    public function search()
    {
        $this->model()->where(function ($query) {
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

        return $this;
    }

    /**
     * Default query
     */
    public function orders() {
        $orderUtil = $this->collect([
            'orders.*',
            'packages.display_name as package_display_name',
            'regions.code as region_code', 'regions.name as region_name',
            'users.name as user_name', 'users.phone as user_phone', 'users.status as user_status'
        ]);

        $orderUtil->model()->leftJoin('packages', 'packages.id', 'orders.package_id');
        $orderUtil->model()->leftJoin('invoices', 'invoices.id', 'orders.invoice_id');
        $orderUtil->model()->leftJoin('regions', 'regions.id', 'orders.region_id');
        $orderUtil->model()->leftJoin('users', 'users.id', 'invoices.user_id');

        $orderUtil->model()->with([
            'invoice',
            'invoice.user',
            'items'
        ]);

        return $orderUtil;
    }
}
