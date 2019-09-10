<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Utils\FilterUtil;
use App\Utils\OrderUtil;
use App\Utils\TimeUtil;

class WidgetOrderController extends Controller
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

    public function getOrderCount()
    {
        $today = [
            Carbon::today(),
            Carbon::today()
        ];

        $yesterday = [
            Carbon::today()->subDay(1),
            Carbon::today()->subDay(1)
        ];

        $lastServenDays = [
            Carbon::today()->subDay(7),
            Carbon::today()
        ];

        $thisMonth = [
            Carbon::today()->startOfMonth(),
            Carbon::today()->endOfMonth()
        ];

        $data = [
            'today' => $this->countByTimeRange($today[0], $today[1]),
            'yesterday' => $this->countByTimeRange($yesterday[0], $yesterday[1]),
            'lastSevenDays' => $this->countByTimeRange($lastServenDays[0], $lastServenDays[1]),
            'thisMonth' => $this->countByTimeRange($thisMonth[0], $thisMonth[1]),
        ];

        return response()->json($data);
    }

    public function countByTimeRange($start, $end)
    {
        $this->filter = [
            'time' => [
                $this->timeUtil->createTimeRange($start, $end)->start->toDateTimeString(),
                $this->timeUtil->createTimeRange($start, $end)->end->toDateTimeString()
            ],
            'status' => ['Pending', 'Confirm', 'On The Way', 'Process', 'Done'],
            'user_status' => ['user'],
            'payment_status' => ['Paid', 'Unpaid'],
            'order_source' => ['Online', 'Offline']
        ];

        $orders = $this->orderUtil->collect()
            ->filter($this->filter)
            ->get();

        return $orders->count();
    }

    public function getStatusPieChart()
    {
        $this->filter = [
            'time' => [
                $this->timeUtil->createDateInMonthRange($this->request->date)->start->toDateTimeString(),
                $this->timeUtil->createDateInMonthRange($this->request->date)->end->toDateTimeString()
            ],
            'status' => ['Pending', 'Confirm', 'On The Way', 'Process', 'Done'],
            'user_status' => ['user'],
            'payment_status' => ['Paid', 'Unpaid'],
            'order_source' => ['Online', 'Offline']
        ];

        $orders = $this->orderUtil->collect()
            ->filter($this->filter)
            ->get();

        $groupedOrders = $orders->groupBy('status')
            ->map(function ($orders) {
                return count($orders);
            });

        $data = [
            'origin' => $groupedOrders,
            'labels' => $groupedOrders->keys(),
            'data' => $groupedOrders->values(),
            'backgroundColor' => $groupedOrders->keys()->map(function ($status) {
                $colors = [
                    'Pending' => '#FF9800',
                    'Cancel' => '#E91E63',
                    'Confirm' => '#2196F3',
                    'On The Way' => '#9C27B0',
                    'Process' => '#795548',
                    'Done' => '#009688'
                ];

                return $colors[$status];
            })
        ];

        return response()->json($data);
    }
}
