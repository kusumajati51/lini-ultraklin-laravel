<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Utils\OrderUtil;
use App\Utils\TimeUtil;

class WidgetIncomeController extends Controller
{
    protected $filter;
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

    public function getDailyIncomeLineChart()
    {
        $time = $this->timeUtil->createDateInMonthRange($this->request->date);

        $this->filter = [
            'time' => [
                $time->start->toDateTimeString(),
                $time->end->toDateTimeString()
            ],
            'status' => ['Pending', 'Confirm', 'On The Way', 'Process', 'Done'],
            'user_status' => ['user'],
            'payment_status' => ['Paid', 'Unpaid'],
            'order_source' => ['Online', 'Offline']
        ];
        
        $daysRange = range(
            $time->start->copy()->addDay(1)->day,
            $time->end->day
        );

        $orders = $this->orderUtil->collect()
            ->filter($this->filter)
            ->get();

        $orders = $orders->map(function ($order) {
            $item = [
                'month' => $order->invoice->created_at->month,
                'day' => $order->invoice->created_at->day,
                'date' => $order->invoice->created_at->toDateString(),
                'status' => strtolower($order->invoice->status),
                'total' => $order->final_total
            ];

            return (object) $item;
        })
        ->groupBy('status');

        $data = [
            'labels' => $daysRange,
            'data' => [
                'paid' => [],
                'unpaid' => []
            ],
            'counter' => [
                'total' => 0,
                'paid' => 0,
                'unpaid' => 0
            ]
        ];

        foreach ($data['data'] as $status => $val) {
            foreach ($daysRange as $i => $day) {
                $data['data'][$status][$i] = 0;
    
                if (isset($orders[$status])) {
                    foreach ($orders[$status] as $j => $order) {
                        if ($day == 1 && $order->month == $time->start->month && $order->day == $time->start->day) {
                            $data['data'][$status][0] += $order->total;
                        } else if ($day == $order->day) {
                            $data['data'][$status][$i] += $order->total;
                        }
                    }
                }
            }
        }

        $data['counter']['total'] = currency(array_sum($data['data']['paid']) + array_sum($data['data']['unpaid']));
        $data['counter']['paid'] = currency(array_sum($data['data']['paid']));
        $data['counter']['unpaid'] = currency(array_sum($data['data']['unpaid']));

        return response()->json($data);
    }
}
