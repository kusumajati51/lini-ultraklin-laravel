<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\V1\Order;
use App\User;

use App\Utils\TimeUtil;

class SalesReportController extends Controller
{
    protected $filter, $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createFilter()
    {
        $timeUtil = new TimeUtil;
        $dateRange = $timeUtil->createTimeRange();

        $this->filter = [
            'codes' => [],
            'date' => [ $dateRange->start, $dateRange->end ]
        ];

        if ($this->request->has('codes') && is_array($this->request->codes) && count($this->request->codes) > 0) {
            $this->filter['codes'] = $this->request->codes;
        }

        if ($this->request->has('date') && is_array($this->request->date) && count($this->request->date) == 2) {
            $dateRange = $timeUtil->createTimeRange($this->request->date[0], $this->request->date[1]);

            $this->filter['date'] = [ $dateRange->start, $dateRange->end ];
        }
    }

    public function orders()
    {
        $this->createFilter();

        $sales = User::select([
            'code', 'name', 'phone', 'email'
        ])
        ->where('status', 'sales')
        ->where(function ($query) {
            if (count($this->filter['codes']) > 0) {
                $query->whereIn('code', $this->filter['codes']);
            }
        })
        ->get();

        $sales->map(function ($sales) {
            $orders = Order::select([
                'orders.*',
                'users.id as user_id', 'users.name as user_name', 'users.phone as user_phone',
            ])
            ->leftJoin('invoices', 'invoices.id', 'orders.invoice_id')
            ->leftJoin('users', 'users.id', 'invoices.user_id')
            ->whereHas('invoice', function ($invoice) use ($sales) {
                $invoice->whereHas('user', function ($user) use ($sales) {
                    $user->where('referral', $sales->code);
                });
            })
            ->whereBetween('orders.created_at', $this->filter['date'])
            ->get();

            $orders->map(function ($order) {
                $prevOrderCount = Order::select(['id'])
                    ->whereHas('invoice', function ($invoice) use ($order) {
                        $invoice->where('user_id', $order->user_id);
                    })
                    ->where('created_at', '<', $order->created_at)
                    ->count();

                $order->sequence = $prevOrderCount + 1;
            });

            $sales->orders = $orders;
        });

        if ($this->request->has('export') && $this->request->export == 1) return $this->export($sales);

        return response()->json([
            'data' => $sales,
            'filter' => $this->filter
        ]);
    }

    public function export($data)
    {
        $filename = 'REPORT_SALES_ORDERS__'.$this->filter['date'][0].'-'.$this->filter['date'][1];

        \Excel::create($filename, function ($excel) use ($data) {
            $excel->sheet('Report', function ($sheet) use ($data) {
                $row = 0;

                $sheet->setStyle([
                    'font' => [
                        'size' => 11
                    ]
                ]);

                foreach ($data as $agent) {
                    $row += 1;

                    $sheet->mergeCells("A{$row}:S{$row}");

                    $sheet->cells("A{$row}:S{$row}", function ($cell) {
                        $cell->setFontSize(14);
                        $cell->setFontWeight('bold');
                    });

                    $sheet->row($row, [
                        strtoupper("{$agent->code} - {$agent->name}")
                    ]);

                    $row += 1;

                    $sheet->cells("A{$row}:S{$row}", function ($cell) {
                        $cell->setFontWeight('bold');
                    });

                    $sheet->row($row, [
                        'Order Time',
                        'Delivery Time',
                        'Order Code',
                        'Invoice Code',
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
                        'Month',
                        'Sequence'
                    ]);

                    foreach ($agent->orders as $order) {
                        $row += 1;

                        $sheet->row($row, [
                            $order->date,
                            isset($order->detail['delivery_date']) ? $order->detail['delivery_date'] : null,
                            $order->code,
                            $order->invoice->code,
                            $order->user->name,
                            $order->package->display_name,
                            $order->package->service->display_name,
                            $order->hasDiscount() ? $order->promotion->code : null,
                            $order->total,
                            $order->discount,
                            $order->final_total,
                            $order->status,
                            $order->invoice->payment,
                            $order->invoice->status,
                            $order->region->name,
                            $order->location,
                            $order->created_at->toDateTimeString(),
                            $order->created_at->format('F'),
                            $order->sequence
                        ]);
                    }

                    $row += 1;
                }
            });
        })->export('xlsx');
    }
}
