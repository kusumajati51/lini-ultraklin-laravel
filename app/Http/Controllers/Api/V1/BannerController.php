<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Banner;

class BannerController extends Controller
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

    public function setFilterTarget()
    {
        if ($this->request->has('target')) {
            $this->filter['target'] = $this->request->target;
        }
        else {
            $this->filter['target'] = 'ALL';
        }
    }

    public function setFilter() {
        $this->setFilterRegion();
        $this->setFilterTarget();
    }

    public function getList()
    {
        $this->setFilter();

        $banners = Banner::select(
            'banners.name', 'banners.description', 'banners.file', 'banners.target',
            'regions.code as region_code', 'regions.name as region_name'
        )
        ->leftJoin('regions', 'regions.id', 'banners.region_id')
        ->where('banners.active', true)
        ->where(function ($query) {
            if (strtoupper($this->filter['region']) != 'ALL') {
                $query->whereHas('region', function ($region) {
                    $region->where('id', $this->request->region)
                        ->orWhere('code', $this->request->region);
                })
                ->orWhereNull('region_id');
            }
        })
        ->where(function ($query) {
            if (strtoupper($this->filter['target']) != 'ALL') {
                $query->where('target', strtolower($this->filter['target']))
                    ->orWhereNull('target');
            }
        })
        ->get();

        return response()->json($banners);
    }
}
