<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Region;

class RegionController extends Controller
{
    protected $filter;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $regions = Region::orderBy('name')
            ->paginate(24);

        return view('admin.region.index', compact(
            'regions'
        ));
    }
}
