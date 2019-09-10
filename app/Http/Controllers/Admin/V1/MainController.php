<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    protected $menu;

    public function index()
    {
        
        return view('admin.__v1.main');
    }
}
