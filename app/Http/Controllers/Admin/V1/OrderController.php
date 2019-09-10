<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\V1\Order;

use App\Utils\FilterUtil;
use App\Utils\OrderUtil; 
use App\Utils\TimeUtil;
use App\Traits\V1\FcmTokenTrait;

class OrderController extends Controller
{
    use FcmTokenTrait;

    protected $filter;
    protected $request;

    protected $filterUtil;
    protected $orderUtil;
    protected $timeUtil;

    public function __construct(
        Request $request,
        FilterUtil $filterUtil,
        OrderUtil $orderUtil,
        TimeUtil $timeUtil
    )
    {
        $this->request = $request;

        $this->filterUtil = $filterUtil;
        $this->orderUtil = $orderUtil;
        $this->timeUtil = $timeUtil;
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

    public function jsonIndex()
    {
        $this->createFilter(['ref_code']);

        $this->orderUtil->collect([
            'orders.*',
            'packages.display_name as package_display_name',
            'regions.code as region_code', 'regions.name as region_name',
            'users.name as user_name', 'users.phone as user_phone', 'users.status as user_status'
        ]);

        $this->orderUtil->model()->leftJoin('packages', 'packages.id', 'orders.package_id');
        $this->orderUtil->model()->leftJoin('invoices', 'invoices.id', 'orders.invoice_id');
        $this->orderUtil->model()->leftJoin('regions', 'regions.id', 'orders.region_id');
        $this->orderUtil->model()->leftJoin('users', 'users.id', 'invoices.user_id');

        $this->orderUtil->filter($this->filter);

        if ($this->request->has('export')) {
            $this->exportOrders('UK', $this->orderUtil->get());
        }

        $orders = $this->orderUtil->paginate()->toArray();
        $orders['filter'] = $this->filter;

        return response()->json($orders);
    }

    public function jsonShow($code)
    {
        $order = Order::select('orders.*')
            ->with([
                'package'
            ])
            ->where('code', $code)
            ->first();

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ], 400);
        }

        $order->visiblePrices();
        $order->visibleHumanPrices();

        $order->items->map(function ($item) {
            $item->visiblePrices();
            $item->visibleHumanPrices();

            return $item;
        });

        $order->user = $order->invoice->user;
        $order->created_by = $order->invoice->created_by;
        $order->updated_by = $order->invoice->updated_by;

        return response()->json($order);
    }

    public function getOrdersByUpline($code)
    {
        $this->request->request->add([
            'ref_code' => $code
        ]);

        $this->filterUtil->setFilter('order', $this->request->all());

        $this->orderUtil->collect([
            'orders.*',
            'packages.display_name as package_display_name',
            'regions.code as region_code', 'regions.name as region_name',
            'users.name as user_name', 'users.phone as user_phone', 'users.status as user_status'
        ]);

        $this->orderUtil->model()->leftJoin('packages', 'packages.id', 'orders.package_id');
        $this->orderUtil->model()->leftJoin('invoices', 'invoices.id', 'orders.invoice_id');
        $this->orderUtil->model()->leftJoin('regions', 'regions.id', 'orders.region_id');
        $this->orderUtil->model()->leftJoin('users', 'users.id', 'invoices.user_id');

        $this->orderUtil->filter($this->filterUtil->getFilter('order'));

        if ($this->request->has('export')) {
            $this->exportOrders($code, $this->orderUtil->get());
        }
        
        $orders = $this->orderUtil->paginate();

        return response()->json($orders);
    }

    public function getOrderByUpline($code, $orderCode)
    {
        $this->filter = [
            'code' => $code,
            'orderCode' => $orderCode
        ];

        $order = Order::select('orders.*')
        ->whereHas('invoice', function ($invoice) {
            $invoice->whereHas('user', function ($user) {
                $user->where('referral', $this->filter['code']);
            });
        })
        ->where(function ($query) {
            $query->where('id', $this->filter['orderCode'])
                ->orWhere('code', $this->filter['orderCode']);
        })
        ->with('package')
        ->first();

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ]);
        }

        $order->visiblePrices();
        $order->visibleHumanPrices();

        $order->items->map(function ($item) {
            $item->visiblePrices();
            $item->visibleHumanPrices();

            return $item;
        });

        $order->user = $order->invoice->user;

        return response()->json($order);
    }

    public function updateStatus($code)
    {
        $order = Order::where('id', $code)
            ->orWhere('code', $code)
            ->first();

        if (is_null($order)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ]);
        }

        $order->status = $this->request->status;
        $order->save();

        return response()->json([
            'success' => 1,
            'message' => 'Order status updated.'
        ]);
    }

    public function exportOrders($code, $data)
    {
        $filename = $code.'__Order_Report__'.$this->request->time[0].'_'.$this->request->time[1];

        \Excel::create($filename, function ($excel) use ($data) {
            $excel->sheet('Sheet 1', function ($sheet) use ($data) {
                $sheet->setColumnFormat([
                    'A' => 'yyyy-mm-dd',
                    'B' => 'yyyy-mm-dd'
                ]);

                $sheet->row(1, [
                    'Order Time',
                    'Delivery Time',
                    'Invoice Code',
                    'Order Code',
                    'Customer',
                    'Package',
                    'Service',
                    'Promo',
                    'Total',
                    'Discount',
                    'Final Total',
                    'Status',
                    'Payment',
                    'Payment Status',
                    'Region',
                    'Location',
                    'Created At'
                ]);

                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                });

                foreach ($data as $i => $row) {
                    $sheet->row($i + 2, [
                        $row->date,
                        isset($row->detail['delivery_date']) ? $row->detail['delivery_date'] : null,
                        $row->code,
                        $row->invoice->code,
                        $row->user->name,
                        $row->package->display_name,
                        $row->package->service->display_name,
						$row->hasDiscount() ? $row->promotion->code : null,
                        $row->total,
                        $row->discount,
                        $row->final_total,
                        $row->status,
                        $row->invoice->payment,
                        $row->invoice->status,
                        $row->region->name,
                        $row->location,
                        $row->created_at->toDateTimeString()
                    ]);
                }
            });
        })->export('xlsx');
    }
}
