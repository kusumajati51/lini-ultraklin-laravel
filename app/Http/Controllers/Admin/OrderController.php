<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\V1\Order;
use App\V1\Invoice;

use App\Utils\InvoiceUtil;
use App\Utils\OrderUtil;
use App\Utils\TimeUtil;

use App\Traits\Telegram as TelegramTrait;
use App\Traits\V1\FcmTokenTrait;

class OrderController extends Controller
{
    use TelegramTrait, FcmTokenTrait;

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

    public function index()
    {
        $this->setFilter();

        $this->orderUtil->setFilter($this->filter);

        $orders = $this->orderUtil->collect()
            ->useMainFilter()
            ->search()
            ->paginate();

        $this->resetTimeFilter();

        $filter = $this->filter;

        $orders->appends($filter);

        $options = [
            'region' => auth('officer')->user()
                ->regions()
                ->select('code as value', 'name as label')
                ->get()
        ];

        return view('admin.order.index', compact(
            'orders',
            'filter',
            'options'
        ));
    }

    public function show($id)
    {
        return view('admin.order.show', compact(
            'id'
        ));
    }

    public function jsonShow($id)
    {
        $order = Order::byRegion()
            ->where('id', $id)
            ->with([
                'package.service',
                'invoice.user',
                'invoice.customer',
                'items'
            ])
            ->first();

        $order->items = $order->items->map(function ($item) {
            $item->extraAttributes([
                'prices',
                'human_prices'
            ]);
        });

        $order->extraAttributes([
            'user', 'prices', 'human_prices'
        ]);

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
        if (!$this->request->has('status')) {
            return response()->json([
                'error' => 'Status invalid.'
            ]);
        }

        $order = Order::find($id);
        $order->status = $this->request->status;
        $order->save();

        InvoiceUtil::calculateCanceledAmount($order->invoice);
        InvoiceUtil::calculateCanceledDiscount($order->invoice);

        $invoice = Invoice::find($order->invoice_id);

        $param = [
            'body' => 'Status order untuk '.$invoice->code.', berubah menjadi '.$order->status,
            'title' => 'Status order anda berubah'
        ];

        $data = [
            'order_id' => $id,
            'status' => $order->status
        ];

        $this->sendNotification($param, $invoice->user_id, $data);

        $this->notifyStatusChanged($order);

        return response()->json([
            'success' => 'Status has changed.'
        ]);
    }

    public function notifyStatusChanged($order)
    {
        $notif = config('ultraklin.order_notification');

        $customer = $order->user->name;

        switch ($order->status) {
            case 'Cancel':
                if (env('TELEGRAM') && $notif['cancel']) {
                    $config = config('ultraklin');

                    $message = '';

                    $message .= '*ORDER CANCELED*';
                    $message .= "\n\n";
                    $message .= "\xF0\x9F\x93\x83  `".$order->code."`";
                    $message .= "\n";
                    $message .= "\xF0\x9F\x91\xA4  `".$customer."`";
                    $message .= "\n\n";

                    $message .= "\xF0\x9F\x8F\xA0 `".strtoupper($order->region->name)."`";
                    $message .= "\n";
                    $message .= "- `".$order->package->display_name."`";

                    $message .= "\n\n";
                    $message .= $config['emoji']['crying'];

                    $this->sendMessage($message);
                }
                break;

            case 'Confirm':
                if (env('TELEGRAM') && $notif['confirm']) {
                    $config = config('ultraklin');

                    $message = '';

                    $message .= '*ORDER CONFIRMED*';
                    $message .= "\n\n";
                    $message .= "\xF0\x9F\x93\x83  `".$order->code."`";
                    $message .= "\n";
                    $message .= "\xF0\x9F\x91\xA4  `".$customer."`";
                    $message .= "\n\n";

                    $message .= "\xF0\x9F\x8F\xA0 `".strtoupper($order->region->name)."`";
                    $message .= "\n";
                    $message .= "- `".$order->package->display_name."`";

                    $message .= "\n\n";
                    $message .= $config['emoji']['thumb'];

                    $this->sendMessage($message);
                }
                break;

            case 'On The Way':
                if (env('TELEGRAM') && $notif['on_the_way']) {
                    $config = config('ultraklin');

                    $message = '';

                    $message .= '*ON THE WAY*';
                    $message .= "\n\n";
                    $message .= "\xF0\x9F\x93\x83  `".$order->code."`";
                    $message .= "\n";
                    $message .= "\xF0\x9F\x91\xA4  `".$customer."`";
                    $message .= "\n\n";

                    $message .= "\xF0\x9F\x8F\xA0 `".strtoupper($order->region->name)."`";
                    $message .= "\n";
                    $message .= "- `".$order->package->display_name."`";

                    $message .= "\n\n";
                    $message .= $config['emoji']['minibus'];

                    $this->sendMessage($message);
                }
                break;

            case 'Process':
                if (env('TELEGRAM') && $notif['process']) {
                    $config = config('ultraklin');

                    $message = '';

                    $message .= '*ON PROCESS*';
                    $message .= "\n\n";
                    $message .= "\xF0\x9F\x93\x83  `".$order->code."`";
                    $message .= "\n";
                    $message .= "\xF0\x9F\x91\xA4  `".$customer."`";
                    $message .= "\n\n";

                    $message .= "\xF0\x9F\x8F\xA0 `".strtoupper($order->region->name)."`";
                    $message .= "\n";
                    $message .= "- `".$order->package->display_name."`";

                    $message .= "\n\n";
                    $message .= $config['emoji']['hourglass'];

                    $this->sendMessage($message);
                }
                break;

            case 'Done':
                if (env('TELEGRAM') && $notif['done']) {
                    $config = config('ultraklin');

                    $message = '';

                    $message .= '*DONE*';
                    $message .= "\n\n";
                    $message .= "\xF0\x9F\x93\x83  `".$order->code."`";
                    $message .= "\n";
                    $message .= "\xF0\x9F\x91\xA4  `".$customer."`";
                    $message .= "\n\n";

                    $message .= "\xF0\x9F\x8F\xA0 `".strtoupper($order->region->name)."`";
                    $message .= "\n";
                    $message .= "- `".$order->package->display_name."`";

                    $message .= "\n\n";
                    $message .= $config['emoji']['party_popper'];

                    $this->sendMessage($message);
                }
                break;
        }
    }
}
