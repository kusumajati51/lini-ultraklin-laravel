<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Item;

class ItemController extends Controller
{
    protected $service;
    protected $package;

    public function index($service = null, $package = null)
    {
        $this->service = $service;
        $this->package = $package;

        $items = Item::select('service_items.*', 'services.display_name as service', 'service_prices.price', 'service_packages.display_name as package')
            ->leftJoin('services', 'services.id', 'service_items.service_id')
            ->leftJoin('service_prices', 'service_prices.item_id', 'service_items.id')
            ->leftJoin('service_packages', 'service_packages.id', 'service_prices.package_id')
            ->where(function ($query) {
                return $query->where('service_items.active', 1)
                    ->whereHas('packages');
            });

        if (!is_null($this->service)) {
            $items = $items->whereHas('service', function ($query) {
                return $query->where('name', $this->service);
            });
        }

        if (!is_null($this->package)) {
            $items = $items->whereHas('packages', function ($query) {
                return $query->where('name', $this->package);
            });
        }


        $items = $items->get();

        return response()->json([
            'data' => $items
        ]);
    }
}
