<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Region;

class RegionController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getList()
    {
        $regions = Region::select('id', 'code', 'name')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json($regions);
    }
}
