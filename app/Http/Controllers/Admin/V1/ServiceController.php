<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Service;

class ServiceController extends Controller
{
    public function jsonGetList()
    {
        $services = Service::orderBy('name', 'asc')
            ->where('active', true)
            ->get();

        return response()->json($services);
    }
}
