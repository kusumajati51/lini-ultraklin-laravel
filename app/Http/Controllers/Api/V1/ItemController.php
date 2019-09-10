<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Item;

class ItemController extends Controller
{
    protected $filter;
    protected $package;
    protected $request;
    protected $service;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setFilterRegion()
    {
        if ($this->request->has('region')) {
            $this->filter['region'] = $this->request->region;
        }
        else {
            $this->filter['region'] = 'ALL';
        }
    }

    public function setFilterPackage()
    {
        if ($this->request->has('package')) {
            $this->filter['package'] = $this->request->package;
        }
        else {
            $this->filter['package'] = 'ALL';
        }
    }

    public function setFilter()
    {
        $this->setFilterRegion();
        $this->setFilterPackage();
    }

    /**
     * @queryString region, package, search
     */
    public function getList()
    {
        $this->setFilter();

        $items = Item::select(
            'items.id', 'items.name', 'items.description',
            'package_item.price', 'packages.name as package_name', 'packages.display_name as package_display_name'
        )
        ->leftJoin('package_item', 'items.id', 'package_item.item_id')
        ->leftJoin('packages', 'package_item.package_id', 'packages.id')
        ->leftJoin('regions', 'regions.id', 'packages.region_id')
        ->where('items.active', true)
        ->where('packages.active', true)
        ->where('regions.active', true)
        ->where(function ($query) {
            if (strtoupper($this->filter['region']) != 'ALL') {
                $query->where('regions.id', $this->filter['region'])
                    ->orWhere('regions.code', $this->filter['region']);
            }

            if (strtoupper($this->filter['package']) != 'ALL') {
                $query->where('packages.id', $this->filter['package'])
                    ->orWhere('packages.name', $this->filter['package']);
            }
        })
        ->where(function ($query) {
            if ($this->request->has('search')) {
                $query->where('item.name', 'like', '%'.$this->request->search.'%');
            }
        })
        ->orderBy('items.name')
        ->get();

        return response()->json($items);
    }
}
