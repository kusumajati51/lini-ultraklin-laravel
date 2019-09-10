<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Item;
use App\Package;

class PackageController extends Controller
{
    protected $filter = [];
    protected $request;

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

    public function setFilterService()
    {
        if ($this->request->has('service')) {
            $this->filter['service'] = $this->request->service;
        }
        else {
            $this->filter['service'] = 'ALL';
        }
    }

    public function setFilter()
    {
        $this->setFilterRegion();
        $this->setFilterService();
    }

    /**
     * @queryString region
     */
    public function getList()
    {
        $this->setFilter();

        $packages = Package::select(
            'packages.name', 'packages.display_name', 'packages.description',
            'services.name as service_name', 'services.display_name as service_display_name',
            'regions.code as region_code', 'regions.name as region_name'
        )
        ->where(function ($query) {
            if (strtoupper($this->filter['region']) != 'ALL') {
                $query->whereHas('region', function ($region) {
                    $region->where('id', $this->filter['region'])
                        ->orWhere('code', $this->filter['region']);
                });
            }

            if (is_array($this->filter['service'])) {
                $query->whereHas('service', function ($service) {
                    $service->whereIn('name', $this->filter['service']);
                });
            }
            else if (is_string($this->filter['service']) && strtoupper($this->filter['service']) != 'ALL') {
                $query->whereHas('service', function ($service) {
                    $service->where('name', $this->filter['service']);
                });
            }
        })
        ->where('packages.active', true)
        ->leftJoin('services', 'services.id', 'packages.service_id')
        ->leftJoin('regions', 'regions.id', 'packages.region_id')
        ->orderBy('packages.name')
        ->get();

        return response()->json($packages);
    }

    /**
     * @queryString search
     */
    public function getItemList($package)
    {
        $package = Package::where('name', $package)->first();

        if (is_null($package)) return [];

        $items = $package->items()
            ->select('items.id', 'items.name', 'items.description')
            ->where('items.active', true)
            ->where(function ($query) {
                return $query->where('name', 'like', '%'.$this->request->search.'%');
            })
            ->orderBy('items.name')
            ->get();

        $items = $items->map(function ($value) {
            $item = [
                'id' => $value->id,
                'name' => $value->name,
                'price' => $value->pivot->price,
                'description' => $value->description
            ];

            return $item;
        });

        return response()->json($items);
    }
}
