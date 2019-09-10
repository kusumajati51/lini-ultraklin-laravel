<?php

namespace App\Http\Controllers\Old;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Item;

class ItemController extends Controller
{
    protected $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getLaundryPiece()
    {
        $items = Item::whereHas('packages', function ($query) {
            return $query->where('name', 'laundry-pieces-regular');
        })
        ->where('items.active', true)
        ->leftJoin('package_item', 'package_item.item_id', 'items.id')
        ->select('items.id', 'items.name', 'package_item.price')
        ->get();

        $items->map(function ($item) {
            $item->price = (string) $item->price;
            
            return $item;
        });
    
        return response()->json([
            'data' => $items
        ]);
    }
}
