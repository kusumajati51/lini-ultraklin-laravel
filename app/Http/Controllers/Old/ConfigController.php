<?php

namespace App\Http\Controllers\Old;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    protected $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $config = config('ultraklin');

        return [
            'minimal' => (string) $config['old']['minimal'],
            'perKilo' => (string) $config['old']['perKilo']
        ];
    }
}
