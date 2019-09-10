<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\Traits\OrderTrait;

class OrderController extends Controller
{
    use OrderTrait;

    protected $filter;
    protected $orders;
    protected $request;
    protected $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setFilter()
    {
        $this->filter = [
            'status' => 'all',
            'sort' => ['created_at', 'desc']
        ];

        if ($this->request->has('status')) {
            $this->filter['status'] = $this->request->status;
        }

        if ($this->request->has('sort')) {
            $this->filter = array_set($this->filter, 'sort', $this->request->sort);
        }
    }

    public function index()
    {
        $this->setFilter();

        $this->user = $this->request->user();

        $orders = $this->user->orders()
            ->with([
                'invoice',
                'items'
            ])
            ->where(function ($query) {
                if ($this->filter['status'] != 'all') {
                    $query->where(DB::raw('LOWER(orders.status)'), strtolower($this->request->status));
                }
            })
            ->orderBy('orders.'.$this->filter['sort'][0], $this->filter['sort'][1])
            ->paginate(24);

        $orders->appends($this->filter);

        return response()->json($orders);
    }

    public function show($id)
    {
        $this->user = $this->request->user();

        $order = $this->user->orders()
            ->where('orders.id', $id)
            ->with([
                'items'
            ])
            ->first();

        if (is_null($order)) {
            return response()->json([
                'error' => 'Order not found.'
            ]);
        }

        return response()->json($order);
    }
}
