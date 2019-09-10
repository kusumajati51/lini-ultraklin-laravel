<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\OrderRepository;

class StoreController extends Controller
{
    protected $order, $request;

    public function __construct(Request $request, OrderRepository $order)
    {
        $this->request = $request;
        $this->order = $order;
    }

    public function getOrderHistories()
    {
        $order = $this->order->collect()->filter(
            $this->request->only([
                'limit', 'sort', 'time', 'search'
            ]),
            [
                'region', 'service', 'referral', 'order_source', 'user_status', 'payment_status'
            ]
        );

        $order->addSelector(new \App\Repositories\Selector\Order\LastStatusFromStore, $this->request->user()->store->id);

        $order->addCriteria(new \App\Repositories\Criteria\Order\ByStore, $this->request->user()->store->id);
        $order->addCriteria(new \App\Repositories\Criteria\Order\InLastStatusFromStore, [
            'store_id' => $this->request->user()->store->id,
            'status' => $this->request->status
        ]);

        $orders = $order->paginate();
        $data = $orders->items();
        $pagination = collect($orders)->except('data');

        $response = (object) [
            'data' => $data,
            'pagination' => $pagination,
            'filter' => $order->getFilter()
        ];

        return response()->json($response);
    }
}
