<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\Utils\StoreUtil;

use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    protected $order, $request;

    public function __construct(Request $request, OrderRepository $order)
    {
        $this->request = $request;

        $this->order = $order;
    }

    public function isStoreRequest()
    {
        return $this->request->segment(3) == 'store' || $this->request->segment(3) == 'stores';
    }

    public function index()
    {
        $order = $this->order->collect()->filter(
            $this->request->only([
                'limit', 'sort', 'time', 'status', 'search'
            ]),
            [
                'region', 'service', 'referral', 'order_source', 'user_status', 'payment_status'
            ]
        );

        if ($this->isStoreRequest()) {
            $order->addCriteria(new \App\Repositories\Criteria\Order\ByStore, $this->request->user()->store->id);
        }

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

    public function find($id)
    {
        if (auth('api')->check() && $this->isStoreRequest()) {
            $order = $this->orderUtil->orders()->model()
                ->where('orders.store_id', auth('api')->user()->store->id)
                ->where('orders.id', $id)
                ->first();
        }

        $order->visibleFormat();

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Order not found.'
            ], 400);
        }

        return response()->json($order);
    }

    public function updateStatus($id)
    {
        $rules = [
            'status' => 'required|in:Cancel,Pending,Confirm,On The Way,Process,Done'
        ];

        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'errors' => $validator->errors() 
            ], 422);
        }

        if (auth('api')->check() && $this->isStoreRequest()) {
            $order = $this->orderUtil->orders()->model()
                ->where('orders.store_id', auth('api')->user()->store->id)
                ->where('orders.id', $id)
                ->first();
        }

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Order not found.'
            ], 400);
        }

        $order->status = ucwords($this->request->status);

        if ($this->isStoreRequest()) {
            StoreUtil::createHistory($order);
            
            $isCancel = strtolower($this->request->status) == 'cancel';

            /**
             * Remove store from order if cancel
             */
            if ($isCancel) {
                $order->store_id = null;

                /**
                 * Make order status pending
                 */
                $order->status = 'Pending';
    
                StoreUtil::assignOrderIfMatch($order, function ($order) {
                    StoreUtil::createHistory($order);
                });
            }
        }

        $order->save();

        return response()->json([
            'success' => 1,
            'message' => 'Order status updated.',
            'data' => $order
        ]);
    }
}
