<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Banner;
use App\Region;

class ImageController extends Controller
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
            $this->filter['region'] = 'JKT';
        }
    }

    public function setFilterTarget()
    {
        if ($this->request->has('target')) {
            $this->filter['target'] = $this->request->target;
        }
        else {
            $this->filter['target'] = 'app';
        }
    }

    public function setFilter()
    {
        $this->setFilterRegion();
        $this->setFilterTarget();
    }

    public function getBannersList()
    {
        $this->setFilter();

        $banners = Banner::select(
            'banners.name', 'banners.file', 'banners.target',
            'regions.code as region_code', 'regions.name as region_name' )->where(function ($query) {
                if (strtoupper($this->filter['region']) != 'ALL') {
                    $query->where('banners.region_id', null)
                        ->orWhere('regions.id', $this->filter['region'])
                        ->orWhere('regions.code', strtoupper($this->filter['region']));
                }
            })
            ->where(function ($query) {
                if (strtoupper($this->filter['target']) != 'ALL') {
                    $query->where('target', strtolower($this->filter['target']))
                        ->orWhereNull('target');
                }
            })
            ->where('banners.active', true)
            ->leftJoin('regions', 'banners.region_id', 'regions.id')
            ->orderBy('banners.active', 'desc')
            ->orderBy('banners.id', 'desc')
            ->get();

        return response()->json($banners);
    }
}
