<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;

use App\Utils\OrderUtil; 
use App\Utils\StoreUtil;

class OrderController extends Controller
{
    use \App\Traits\V1\OrderTrait;

    protected $filter, $request;

    protected $orderUtil;

    public function __construct(Request $request, OrderUtil $orderUtil)
    {
        $this->request = $request;

        $this->orderUtil = $orderUtil;
    }

    public function isStoreRequest()
    {
        return $this->request->segment(3) == 'store' || $this->request->segment(3) == 'stores';
    }

    public function createFilter($except = [])
    {
        $defaultFilter = $this->orderUtil->getDefaultFilter($except);
        $requestFilter = $this->request->only([
            'time', 'search', 'status', 'user_status', 'order_source', 'region'
        ]);

        $this->filter = collect($defaultFilter)->merge($requestFilter);

        return $this->filter;
    }

    public function orders()
    {
        $orders = $this->orderUtil->collect([
            'orders.*',
            'packages.display_name as package_display_name',
            'regions.code as region_code', 'regions.name as region_name',
            'users.name as user_name', 'users.phone as user_phone', 'users.status as user_status'
        ]);

        $orders->model()->leftJoin('packages', 'packages.id', 'orders.package_id');
        $orders->model()->leftJoin('invoices', 'invoices.id', 'orders.invoice_id');
        $orders->model()->leftJoin('regions', 'regions.id', 'orders.region_id');
        $orders->model()->leftJoin('users', 'users.id', 'invoices.user_id');

        $orders->model()->with([
            'invoice',
            'invoice.user',
            'items'
        ]);

        return $orders;
    }

    public function index()
    {
        $this->createFilter(['ref_code']);

        $orders = $this->orders()->filter($this->filter);

        if (auth('api')->check() && $this->request->segment(3) == 'orders') {
            $orders->model()->whereHas('invoice', function ($invoice) {
                $invoice->where('user_id', $this->user()->id);
            });
        } else if (auth('api')->check() && $this->isStoreRequest()) {
            $orders->model()->where('store_id', $this->user()->store->id);
        }

        if ($this->request->has('export')) {
            $this->exportOrders('UK', $this->orderUtil->get());
        }

        $orders = $orders->paginate()->toArray();
        $orders['filter'] = $this->filter;

        return response()->json($orders);
    }

    public function show($id)
    {
        $this->user = $this->request->user();

        $order = $this->user->orders()
            ->where('orders.id', $id)
            ->with([
                'items',
                'ratings'
            ])
            ->first();

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Order not found.'
            ]);
        }

        return response()->json($order);
    }

    public function find($id)
    {
        if (auth('api')->check() && $this->isStoreRequest()) {
            $order = $this->orders()->model()
                ->where('orders.store_id', $this->user()->store->id)
                ->where('orders.id', $id)
                ->first();
        }

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Order not found.'
            ]);
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
            ]);
        }

        if (auth('api')->check() && $this->isStoreRequest()) {
            $order = $this->orders()->model()
                ->where('orders.store_id', $this->user()->store->id)
                ->where('orders.id', $id)
                ->first();
        }

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Order not found.'
            ]);
        }

        $isCancel = strtolower($this->request->status) == 'cancel';
        $isAssignStore = $isCancel && $hasStore ? true : false;

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
