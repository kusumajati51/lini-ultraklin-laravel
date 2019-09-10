<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Invoice;
use App\Order;
use App\User;

use App\Utils\OrderUtil;
use App\Utils\TimeUtil;

class DashboardController extends Controller
{
    protected $request;
    protected $timeFilter = [];

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

    public function setTimeFilter()
    {
        $this->timeFilter['today'] = [
            Carbon::today()->subDay(1)->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]),
            Carbon::today()->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1])
        ];

        $this->timeFilter['yesterday'] = [
            Carbon::today()->subDay(2)->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]),
            Carbon::today()->subDay(1)->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1])
        ];

        $this->timeFilter['last_seven_days'] = [
            Carbon::today()->subDay(8)->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]),
            Carbon::today()->subDay(1)->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1])
        ];

        $this->timeFilter['this_month'] = [
            Carbon::today()->startOfMonth()->subDay(1)->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]),
            Carbon::today()->endOfMonth()->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1])
        ];
    }

    public function index()
    {
        $this->setTimeFilter();

        $order = $this->getOrderCount();

        $user = $this->getUserCount();

        return view('admin.dashboard.index', compact(
            'order',
            'user'
        ));
    }

    public function getOrderCount()
    {
        $order = [];

        $order['today'] = Order::byRegion()
            ->whereBetween('date', $this->timeFilter['today'])
            ->get(['id'])
            ->count();

        $order['yesterday'] = Order::byRegion()
            ->whereBetween('date', $this->timeFilter['yesterday'])
            ->get(['id'])
            ->count();

        $order['last_seven_days'] = Order::byRegion()
            ->whereBetween('date', $this->timeFilter['last_seven_days'])
            ->get(['id'])
            ->count();

        $order['this_month'] = Order::byRegion()
            ->whereBetween('date', $this->timeFilter['this_month'])
            ->get(['id'])
            ->count();

        return $order;
    }

    public function getUserCount()
    {
        $user = [];

        $user['today'] = User::whereBetween('created_at', $this->timeFilter['today'])
            ->get(['id'])
            ->count();

        $user['yesterday'] = User::whereBetween('created_at', $this->timeFilter['yesterday'])
            ->get(['id'])
            ->count();

        $user['last_seven_days'] = User::whereBetween('created_at', $this->timeFilter['last_seven_days'])
            ->get(['id'])
            ->count();

        $user['this_month'] = User::whereBetween('created_at', $this->timeFilter['this_month'])
            ->get(['id'])
            ->count();

        return $user;
    }

    public function jsonGetIncome()
    {
        $this->filter = [
            'time' => [
                $this->timeUtil->createTimeRange()->start->toDateTimeString(),
                $this->timeUtil->createTimeRange()->end->toDateTimeString(),
            ],
            'status' => ['Pending', 'Confirm', 'On The Way', 'Process', 'Done'],
            'user_status' => ['user'],
            'payment_status' => ['Paid', 'Unpaid'],
            'order_source' => ['Online', 'Offline'],
            'service' => 'all'
        ];

        $time = $this->timeUtil->createDateInMonthRange();

        if ($this->request->has('date')) {
            $time = $this->timeUtil->createDateInMonthRange($this->request->date);
        }

        $this->filter['time'] = [
            $time->start->toDateTimeString(),
            $time->end->toDateTimeString()
        ];
        
        $daysRange = range(
            $time->start->copy()->addDay(1)->day,
            $time->end->day
        );

        $this->orderUtil->setFilter($this->filter);

        $orders = $this->orderUtil->collect()
            ->useMainFilter()
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

        return response()->json(
            $data
        );
    }
}
