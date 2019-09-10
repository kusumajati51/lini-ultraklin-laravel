<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\V1\Package;

use App\Traits\V1\OfficerTrait;

class AdminController extends Controller
{
    use OfficerTrait;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getPackages()
    {
        $packages = Package::whereHas('region', function ($region) {
            $region->whereIn('id', $this->officer()->regions->pluck('id'));
        })
        ->orderBy('display_name', 'asc')
        ->get();

        return response()->json($packages);
    }

    public function getPackagesByRegion($regionId)
    {
        $region = $this->officer()->regions()->find($regionId);

        if (is_null($region)) {
            return response()->json([
                'error' => 1,
                'message' => 'Invalid region.'
            ], 400);
        }

        $packages = Package::where('region_id', $region->id)
            ->orderBy('display_name', 'asc')
            ->get();

        return response()->json($packages);
    }

    public function getRegions()
    {
        $regions = $this->officer()->regions;

        return response()->json($regions);
    }
}
