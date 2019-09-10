<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function jsonGetMenu()
    {
        foreach (config('menu') as $i => $menu) {
            $permission = collect($menu['menu'])->pluck('permission');

            if (!auth('officer')->user()->hasPermissions($permission)) continue;

            if ($i > 0) {
                $this->menu[] = [
                    'divider' => true
                ];
            }

            $this->menu[] = [
                'header' => $menu['header']
            ];

            foreach ($menu['menu'] as $item) {
                if (isset($item['permission']) && auth('officer')->user()->hasPermission($item['permission'])) {
                    $this->menu[] = [
                        'item' => [
                            'title' => $item['title'],
                            'link' => url($item['link']),
                            'link_js' => isset($item['link_js']) ? $item['link_js'] : null
                        ]
                    ];
                }
            }
        }

        return response()->json($this->menu);
    }
}
