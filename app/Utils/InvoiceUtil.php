<?php

namespace App\Utils;

use App\V1\Invoice;

use App\Interfaces\V1\CollectionInterface;

use App\Utils\FilterUtil;

class InvoiceUtil implements CollectionInterface {
    protected $model;

    protected $defaultFilter, $filter, $filterUtil;

    public function __construct(FilterUtil $filterUtil)
    {
        $this->defaultFilter = $filterUtil->getDefaultFilter('invoice');
    }

    public function model()
    {
        return $this->model;
    }

    public function collect($fields = '*')
    {
        $this->model = Invoice::select($fields);

        return $this;
    }

    public function filter($filter = [])
    {
        $this->filter = [];

        if (!isset($filter['limit'])) {
            $this->filter['limit'] = $this->defaultFilter['limit'];
        }

        if (!isset($filter['sort'])) {
            $this->filter['sort'] = $this->defaultFilter['sort'];
        }

        foreach ($filter as $key => $val) {
            if (!isset($this->defaultFilter[$key])) continue;

            $this->filter[$key] = $val;

            if (isset($this->filterMethods()[$key])) {
                $this->{$this->filterMethods()[$key]}();
            }
        }

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function get()
    {
        if (!isset($this->filter['sort'])) {
            $this->filter['sort'] = $this->defaultFilter['sort'];
        }

        return $this->model()
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

    public function find($id = null)
    {
        if (is_null($id)) return null;

        $data = $this->model()->where(function ($query) use ($id) {
            $query->where('id', $id)
                ->orWhere('code', $id);
        })
        ->first();
        
        return $data;
    }

    public function filterMethods()
    {
        return [
            'date' => 'filterByDate',
            'status' => 'filterByStatus',
            'user_status' => 'filterByUserStatus',
            'online' => 'filterByOrderMethod',
            'region' => 'filterByRegion',
            'codes' => 'filterByCodes'
        ];
    }

    public function filterByDate()
    {
        if (empty($this->filter['date'])) return $this;

        $this->model()->whereBetween('created_at', $this->filter['date']);

        return $this;
    }

    public function filterBystatus()
    {
        if (empty($this->filter['status'])) return $this;

        $this->model()->whereIn('status', $this->filter['status']);

        return $this;
    }

    public function filterByUserStatus()
    {
        if (empty($this->filter['codes'])) return $this;

        $this->model()->whereHas('user', function ($user) {
            $user->whereIn('status', $this->filter['user_status']);
        });

        return $this;
    }

    public function filterByOrderMethod()
    {
        if (empty($this->filter['online'])) return $this;

        $this->model()->where('offline', !$this->filter['online']);

        return $this;
    }

    public function filterByRegion()
    {
        if (empty($this->filter['codes'])) return $this;

        $this->model()->whereHas('order', function ($order) {
            $order->whereIn('region_id', $this->filter['region']);
        });

        return $this;
    }

    public function filterByCodes()
    {
        if (empty($this->filter['codes'])) return $this;

        $this->model()->whereIn('code', $this->filter['codes']);

        return $this;
    }

    public static function calculateCanceledAmount($invoice)
    {
        $invoice->canceled_amount = $invoice->orders->sum(function ($order) {
            return (strtolower($order->status) === 'cancel') ? $order->total : 0;
        });

        $invoice->save();

        return $invoice->canceled_amount;
    }

    public static function calculateCanceledDiscount($invoice)
    {
        $invoice->canceled_discount = $invoice->orders->sum(function ($order) {
            return (strtolower($order->status) === 'cancel') ? $order->discount : 0;
        });

        $invoice->save();

        return $invoice->canceled_discount;
    }
}
