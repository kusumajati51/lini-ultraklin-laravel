<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Excel;

use App\V1\Order;
use App\Service;

use App\Utils\OrderUtil;
use App\Utils\TimeUtil;

class ReportController extends Controller
{
    protected $request;

    protected $orderUtil;
    protected $timeUtil;

    public function __construct(
        Request $request,
        OrderUtil $orderUtil,
        TimeUtil $timeUtil
    )
    {
        $this->request = $request;

        $this->orderUtil = $orderUtil;
        $this->timeUtil = $timeUtil;
    }

    public function setFilter() {
        $this->filter = [
            'region' => auth('officer')->user()->regions->pluck('code'),
            'date_mode' => 'daily',
            'date' => date('Y-m-d'),
            'd' => date('Y-m-d'),
            'a' => date('Y-m-d'),
            'time' => [
                $this->timeUtil->createTimeRange()->start->toDateTimeString(),
                $this->timeUtil->createTimeRange()->end->toDateTimeString(),
            ],
            'status' => ['Cancel', 'Pending', 'Confirm', 'On The Way', 'Process', 'Done'],
            'user_status' => ['user'],
            'payment_status' => ['Paid', 'Unpaid'],
            'search' => '',
            'order_source' => ['Online'],
            'service' => 'all',
            'sort' => ['id', 'desc']
        ];

        if ($this->request->has('region')) {
            $this->filter['region'] = $this->request->region;
        }

        if ($this->request->has('date_mode')) {
            $this->filter['date_mode'] = $this->request->date_mode;
        }

        if ($this->request->has('date')) {
            $timeRange = $this->timeUtil->createTimeRange($this->request->date, $this->request->date);

            $this->filter['time'] = [
                $timeRange->start->toDateTimeString(),
                $timeRange->end->toDateTimeString()
            ];
            
            $this->filter['date'] = $this->request->date;
        } else if ($this->request->has('d') && $this->request->has('a')) {
            $timeRange = $this->timeUtil->createTimeRange($this->request->d, $this->request->a);

            $this->filter['time'] = [
                $timeRange->start->toDateTimeString(),
                $timeRange->end->toDateTimeString()
            ];
            $this->filter['d'] = $this->request->d;
            $this->filter['a'] = $this->request->a;
        }

        if ($this->request->has('status')) {
            $this->filter['status'] = $this->request->status;
        }

        if ($this->request->has('user_status')) {
            $this->filter['user_status'] = $this->request->user_status;
        }

        if ($this->request->has('payment_status')) {
            $this->filter['payment_status'] = $this->request->payment_status;
        }

        if ($this->request->has('order_source')) {
            $this->filter['order_source'] = $this->request->order_source;
        }

        if ($this->request->has('service')) {
            $this->filter['service'] = $this->request->service;
        }

        if ($this->request->has('search')) {
            $this->filter['search'] = $this->request->search;
        }
    }

    public function resetTimeFilter()
    {
        if ($this->filter['date_mode'] == 'daily') {
            $this->filter['time'] = [$this->filter['date'], $this->filter['date']];
        } else {
            $this->filter['time'] = [$this->filter['d'], $this->filter['a']];
        }
    }

    public function getOrders()
    {
        $this->setFilter();

        $orders = $this->orderUtil->collect()
            ->filter($this->filter)
            ->get()
            ->map(function ($order) {
                $order->visiblePrices();
                $order->visibleHumanPrices();

                return $order;
            });

        if ($this->request->has('export') && ($this->request->export == 'xlsx' || $this->request->export == 'pdf')) {
            $this->export($orders, $this->request->export);
        }

        $orders = $orders->map(function ($order) {
            $_order = [
                'date' => $order->created_at->toDateString(),
                'code' => $order->code,
                'customer' => $order->user->name,
                'package_name' => $order->package->display_name,
                'promotion_code' => $order->hasDiscount() ? $order->promotion->code : null,
                'payment_status' => $order->invoice->status,
                'prices' => (array) $order->prices,
                'human_prices' => (array) $order->human_prices
            ];

            return $_order;
        });

        $sumedOrders = $orders->groupBy('date')
            ->map(function ($orders, $key) {
                return [
                    'date' => $key,
                    'prices' => [
                        'total' => $orders->sum('prices.total'),
                        'discount' => $orders->sum('prices.discount'),
                        'final_total' => $orders->sum('prices.final_total')
                    ],
                    'human_prices' => [
                        'total' => human_price($orders->sum('prices.total')),
                        'discount' => human_price($orders->sum('prices.discount')),
                        'final_total' => human_price($orders->sum('prices.final_total'))
                    ]
                ];
            });

        $this->resetTimeFilter();

        $filter = $this->filter;

        $options = [
            'region' => auth('officer')->user()
                ->regions()
                ->select('code as value', 'name as label')
                ->get(),
            'service' => Service::orderBy('name')
                ->get(['display_name as label', 'name as value'])
                ->prepend([
                    'label' => 'All service',
                    'value' => 'all'
                ])
        ];

        return view('admin.report.order', compact(
            'orders',
            'sumedOrders',
            'filter',
            'options'
        ));
    }

    public function export($data, $format)
    {
        if ($this->request->has('date_mode') && $this->filter['date_mode'] == 'daily') {
            $filename = 'Order_Report__'.$this->request->date;
        }
        else if ($this->request->has('date_mode') && $this->filter['date_mode'] == 'range') {
            $filename = 'Order_Report__'.$this->request->d.'_'.$this->request->a;
        }
        else {
            $filename = 'Order_Report__'.date('Y-m-d');
        }

        Excel::create($filename, function ($excel) use ($data) {
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
                    'Created At',
                    'Month'
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
                        $row->created_at->toDateTimeString(),
                        $row->created_at->format('F')
                    ]);
                }
            });
        })->export($format);
    }
}
