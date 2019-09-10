<?php

namespace App\Repositories;

use Illuminate\Container\Container as App;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Contracts\CriteriaInterface;
use App\Repositories\Contracts\SelectorInterface;

abstract class Repository implements RepositoryInterface, CriteriaInterface, SelectorInterface {
    private $app;
    
    protected $defaultFilter, $filter, $model;

    public function __construct()
    {
        $this->app = new App;
        
        $this->defaultFilter = $this->defaultFilter();

        $this->makeModel();
    }

    abstract function model();

    abstract function defaultFilter();

    abstract function filterCriteria();

    public function makeModel()
    {
        $model = $this->app->make($this->model());

        return $this->model = $model;
    }

    public function get()
    {
        if (!isset($this->filter['sort'])) {
            $this->filter['sort'] = $this->defaultFilter['sort'];
        }

        return $this->model
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

        return $this->model
            ->orderBy($this->filter['sort'][0], $this->filter['sort'][1])
            ->paginate($this->filter['limit']);
    }

    public function filter(array $requestFilter, array $exceptedKeys = [])
    {
        $defaultFilter = collect($this->defaultFilter())->except($exceptedKeys)->all();
        $filter = collect($defaultFilter)->merge($requestFilter)->all();

        if (!isset($filter['limit'])) {
            $this->filter['limit'] = $this->defaultFilter['limit'];
        }

        if (!isset($filter['sort'])) {
            $this->filter['sort'] = $this->defaultFilter['sort'];
        }

        if (isset($requestFilter['time']) && !is_null($requestFilter['time']) && count($requestFilter['time']) == 2) {
            $time = (new \App\Utils\TimeUtil)->createTimeRange($filter['time'][0], $filter['time'][1]);

            $filter['time'] = [
                $time->start->toDateTimeString(),
                $time->end->toDateTimeString()
            ];
        } else {
            $filter['time'] = $defaultFilter['time'];
        }

        foreach ($filter as $key => $val) {
            if (!isset($defaultFilter[$key])) continue;

            if (is_array($val)) {
                $val = collect($val)->filter(function ($val) {
                    return !is_null($val);
                })
                ->all();
            }

            $this->filter[$key] = is_null($val) || (is_array($val) && count($val) < 1) ? $defaultFilter[$key] : $val;

            $filterCriteria = $this->filterCriteria();

            if (isset($filterCriteria[$key])) {
                $this->model = (new $filterCriteria[$key])->apply($this->model, $this->filter);
            }
        }

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function addCriteria($criteria, $data)
    {
        $this->model = $criteria->apply($this->model, $data);
    }

    public function addSelector($selector, $data)
    {
        $this->model = $selector->apply($this->model, $data);
    }
}
