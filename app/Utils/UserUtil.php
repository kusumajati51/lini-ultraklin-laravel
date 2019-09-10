<?php

namespace App\Utils;

use Carbon\Carbon;

use App\User;

use App\Interfaces\V1\CollectionInterface;

use App\Utils\FilterUtil;
use App\Utils\TimeUtil;

class UserUtil implements CollectionInterface {
    protected $defaultFilter;
    protected $filter;
    protected $users;

    protected $filterUtil;

    public function __construct(FilterUtil $filterUtil)
    {
        $this->filterUtil = $filterUtil;

        $this->defaultFilter = $this->filterUtil->getDefaultFilter('user');
    }

    public function model()
    {
        return $this->users;
    }

    public function collect($fields = '*')
    {
        $this->users = User::select($fields);

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
            $this->filter[$key] = $val;

            if (isset($this->filterMethods[$key])) {
                $this->{$this->filterMethods[$key]}();
            }
        }

        return $this;
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

    public function filterMethods() {
        return [
            'ref_code' => 'filterByUpline',
            'min_order' => 'filterByMinOrder',
            'region' => 'filterByRegion',
            'status' => 'filterByStatus',
            'time' => 'filterByTime',
            'search' => 'search'
        ];
    }

    public function filterByUpline()
    {
        $this->model()
            ->where('users.referral', $this->filter['ref_code']);

        return $this;
    }

    public function filterByMinOrder()
    {
        if (is_null($this->filter['min_order']) || $this->filter['min_order'] < 0) return $this;

        $this->model()
            ->has('orders', '>=', $this->filter['min_order']);

        return $this;
    }

    public function filterByTime()
    {
        $this->model()
            ->whereBetween('users.created_at', $this->filter['time']);

        return $this;
    }

    public function filterByRegion()
    {
        if (is_null($this->filter['region'])) {
            $this->filter['region'] = auth('officer')->user()->regions->pluck('code');
        }

        $this->model()->whereHas('orders', function ($orders) {
            $orders->whereHas('region', function ($region) {
                $region->whereIn('code', $this->filter['region']);
            });
        });

        return $this;
    }

    public function search()
    {
        $this->model()
            ->where(function ($query) {
                $query->where('users.name', 'like', '%'.$this->filter['search'].'%')
                    ->orWhere('users.email', 'like', '%'.$this->filter['search'].'%')
                    ->orWhere('users.phone', 'like', '%'.$this->filter['search'].'%');
            });

        return $this;
    }
}
