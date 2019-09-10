<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Menu;

class MenuController extends Controller
{
    protected $filter;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setNameFilter()
    {
        if ($this->request->has('name')) {
            $this->filter['name'] = strtolower($this->request->name);
        }
        else {
            $this->filter['name'] = 'all';
        }
    }

    public function setFilter()
    {
        $this->setNameFilter();
    }

    public function show($name)
    {
        $menu = Menu::where('name', $name)
            ->with([
                'items' => function ($query) {
                    $query->orderBy('order', 'asc');
                }
            ])
            ->first();

        if (is_null($menu)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ]);
        }

        return response()->json($menu);
    }

    public function getList()
    {
        $this->setFilter();

        $menu = Menu::where(function ($query) {
            if (strtoupper($this->filter['name']) != 'ALL') {
                $query->where('name', $this->filter['name']);
            }
        })
        ->where('active', 1)
        ->with([
            'items' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])
        ->orderBy('display_name', 'asc')
        ->get();

        return response()->json($menu);
    }
}
