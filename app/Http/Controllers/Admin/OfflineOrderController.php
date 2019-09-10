<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use \App\Order;

use \App\Traits\V1\OrderTrait;

class OfflineOrderController extends Controller
{
    use OrderTrait;

    protected $orders;
    protected $request;
    protected $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function user()
    {
        return $this->request->user('officer');
    }

    public function isOfflineOrder()
    {
        return true;
    }

    public function index()
    {
        $orders = Order::whereHas('invoice', function ($query) {
            return $query->doesntHave('user');
        })
        ->paginate(24);

        return view('admin.offline_order.index', compact(
            'orders'
        ));
    }

    public function create()
    {
        $regions = auth('officer')->user()->regions()
            ->select('name as label', 'id as value')
            ->orderBy('name')
            ->get();

        return view('admin.offline_order.create', compact(
            'regions'
        ));
    }
    
    public function show($id)
    {
        if ($this->request->wantsJson()) {
            $order = Order::whereHas('invoice', function ($query) {
                return $query->doesntHave('user');
            })
            ->where('id', $id)
            ->with([
                'package.service',
                'invoice.user'
            ])
            ->first();

            return response()->json($order);
        }

        return view('admin.offline_order.show', compact(
            'id'
        ));
    }
}
