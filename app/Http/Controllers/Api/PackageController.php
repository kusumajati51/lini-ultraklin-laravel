<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Item;
use App\Package;

class PackageController extends Controller
{
    protected $filter;
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
            $this->filter['region'] = 'JKT';
        }
    }

    public function getList(){
        $this->setFilterRegion();

        $packages = Package::select(
            'packages.name', 'packages.display_name',
            'services.name as service_name', 'services.display_name as service_display_name',
            'regions.code as region_code', 'regions.name as region_name'
        )
        ->where(function ($query) {
            if (strtoupper($this->filter['region']) != 'ALL') {
                $query->where('packages.region_id', null)
                    ->orWhere('regions.id', $this->filter['region'])
                    ->orWhere('regions.code', strtoupper($this->filter['region']));
            }
        })
        ->where('packages.active', true)
        ->leftJoin('services', 'services.id', 'packages.service_id')
        ->leftJoin('regions', 'regions.id', 'packages.region_id')
        ->get();

        return response()->json($packages);
    }

    public function getItemsList($package)
    {
        $package = Package::where('name', $package)->first();

        if (is_null($package)) return [];

        $items = $package->items()
            ->select('items.id', 'items.name')
            ->where('items.active', true)
            ->where(function ($query) {
                return $query->where('name', 'like', '%'.$this->request->search.'%');
            })
            ->get();

        $items = $items->map(function ($value) {
            $item = [
                'id' => $value->id,
                'name' => $value->name,
                'price' => $value->pivot->price
            ];

            return $item;
        });

        return response()->json($items);
    }
}
