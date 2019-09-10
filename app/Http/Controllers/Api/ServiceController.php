<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Package;
use App\Service;

class ServiceController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    

    public function getList()
    {
        $services = Service::select('name', 'display_name')
            ->where('active', true)
            ->get();

        return response()->json($services);
    }

    public function getPackageList($serviceName)
    {
        $service = Service::where('name', $serviceName)->first();

        if (is_null($service)) {
            return [];
        }

        $items = Package::whereHas('service', function ($query) use ($service) {
            return $query->where('name', $service->name);
        });

        if ($service->child->count() > 0) {
            $items = Package::whereHas('service', function ($query) use ($service) {
                return $query->whereIn('name', $service->child->pluck('name'));
            });
        }

        $items = $items->where('active', true)
            ->select('name', 'display_name', 'description')
            ->get();

        return response()->json($items);
    }
}
