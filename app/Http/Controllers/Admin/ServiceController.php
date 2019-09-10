<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Service;

class ServiceController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $services = Service::paginate(24);

        return view('admin.service.index', compact(
            'services'
        ));
    }

    public function showPackages($id)
    {
        $service = Service::find($id);

        $packages = $service->packages()
            ->paginate(24);

        return view('admin.service.package', compact(
            'service',
            'packages'
        ));
    }
}
