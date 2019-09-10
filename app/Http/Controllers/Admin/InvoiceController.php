<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Setting;
use App\V1\Invoice;
use App\Traits\V1\FcmTokenTrait;

use App\Utils\OrderItemUtil;

class InvoiceController extends Controller
{
    use FcmTokenTrait;

    protected $orderItemUtil;

    protected $filter;
    protected $request;

    public function __construct(Request $request, OrderItemUtil $orderItemUtil)
    {
        $this->request = $request;
        $this->orderItemUtil = $orderItemUtil;
    }

    public function setDefaultFilter($start = null, $end = null) {
        $this->filter = [
            'sort' => ['id', 'desc']
        ];

        if ($this->request->has('date')) {
            $this->filter = array_set($this->filter, 'date', $this->request->date);
        }
        else if ($this->request->has('d') && $this->request->has('a')) {
            $this->filter = array_set($this->filter, 'd', $this->request->d);
            $this->filter = array_set($this->filter, 'a', $this->request->a);
        }

        if ($this->request->has('status')) {
            $this->filter = array_set($this->filter, 'status', $this->request->status);
        }
        else {
            $this->filter = array_set($this->filter, 'status', ['Paid', 'Unpaid']);
        }

        if ($this->request->has('user_status')) {
            $this->filter = array_set($this->filter, 'user_status', $this->request->user_status);
        }
        else {
            $this->filter = array_set($this->filter, 'user_status', ['user']);
        }

        if ($this->request->has('search')) {
            $this->filter = array_set($this->filter, 'search', $this->request->search);
        }
        else {
            $this->filter = array_set($this->filter, 'search', '');
        }

        if ($this->request->has('online')) {
            $this->filter = array_set($this->filter, 'online', $this->request->online);
        }
        else {
            $this->filter = array_set($this->filter, 'online', 1);
        }

        if ($this->request->has('region')) {
            $this->filter = array_set($this->filter, 'region', $this->request->region);
        }
        else {
            $this->filter = array_set(
                $this->filter,
                'region',
                auth('officer')->user()->regions()->select('code')->pluck('code')
            );
        }

        $this->setTimeFilter();
    }

    public function setTimeFilter($dateMode = 'daily', $start = null, $end = null) {
        $today = Carbon::today();
        $now = Carbon::now();
        $startTime = is_null($start) ? Carbon::today() : Carbon::parse($start);
        $endTime = is_null($end) ? Carbon::today() : Carbon::parse($end);

        $startTime->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]);
        $endTime->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1]);

        $startTime->subDay(1);

        if (is_null($start) && is_null($end) && $now->diffInMinutes($endTime, false) < 0) {
            $startTime->addDay(1);
            $endTime->addDay(1);
        }

        $this->filter = array_set($this->filter, 'date_mode', $dateMode);
        
        $this->filter = array_set($this->filter, 'time', [
            $startTime->toDatetimeString(),
            $endTime->toDatetimeString()
        ]);
    }

    public function resetTimeFilter() {
        if ($this->request->has('date_mode') && $this->request->date_mode == 'daily') {
            $this->filter = array_set($this->filter, 'time', [$this->request->date, $this->request->date]);
        }
        else if ($this->request->has('date_mode') && $this->request->date_mode == 'range') {
            $this->filter = array_set($this->filter, 'time', [$this->request->d, $this->request->a]);
        }
    }

    public function index()
    {
        $this->setDefaultFilter();

        if ($this->request->has('date_mode') && $this->request->date_mode == 'daily') {
            $this->setTimeFilter($this->request->date_mode, $this->request->date, $this->request->date);
        }
        else if ($this->request->has('date_mode') && $this->request->date_mode == 'range') {
            $this->setTimeFilter($this->request->date_mode, $this->request->d, $this->request->a);
        }

        $invoices = Invoice::byRegion()
            ->where(function ($query) {
                if ($this->filter['online']) {
                    $query->whereHas('user', function ($user) {
                        $user->whereIn('status', $this->filter['user_status']);
                    })
                    ->orWhereHas('customer', function ($customer) {
                        $customer->whereHas('client');
                    });
                }
                else {
                    $query->doesntHave('user');
                }
            })
            ->where(function ($query) {
                $query->whereHas('orders', function ($order) {
                    $order->whereHas('region', function ($region) {
                        $region->whereIn('code', $this->filter['region']);
                    });
                })
                ->whereIn('status', $this->filter['status'])
                ->whereBetween('created_at', $this->filter['time']);
            })
            ->where(function ($query) {
                return $query->whereHas('user', function ($user) {
                    return $user->where('name', 'like', '%'.$this->filter['search'].'%')
                        ->orWhere('phone', 'like', '%'.$this->filter['search'].'%')
                        ->orWhere('email', 'like', '%'.$this->filter['search'].'%');
                })
                ->orWhereHas('promotion', function ($promotion) {
                    return $promotion->where('code', 'like', '%'.$this->filter['search'].'%')
                        ->orWhere('name', 'like', '%'.$this->filter['search'].'%');
                })
                ->orWhereHas('orders', function ($order) {
                    return $order->where('location', 'like', '%'.$this->filter['search'].'%');
                })
                ->orWhere('code', 'like', '%'.$this->filter['search'].'%');
            })
            ->orderBy($this->filter['sort'][0], $this->filter['sort'][1])
            ->paginate(24);

        $this->resetTimeFilter();

        $invoices->appends($this->filter);

        $filter = $this->filter;

        $options = [
            'region' => auth('officer')->user()
                ->regions()
                ->select('code as value', 'name as label')
                ->get()
        ];

        return view('admin.invoice.index', compact(
            'invoices',
            'filter',
            'options'
        ));
    }

    public function show($code)
    {
        $invoice = Invoice::byRegion()
            ->where('code', $code)->first();

        return view('admin.invoice.show', compact(
            'code', 'invoice'
        ));
    }

    public function jsonShow($code)
    {
        $invoice = Invoice::byRegion()
            ->where('code', $code)
            ->with([
                'user',
                'customer',
                'promotion',
                'orders.ratings',
                'orders.items',
                'orders.package.service'
            ])
            ->first();

        if (is_null($invoice)) {
            return response()->json([
                'error' => 1,
                'message' => 'Invoice not found.'
            ]);
        }

        $invoice->visiblePrices();
        $invoice->visibleHumanPrices();

        $invoice->orders->map(function ($order) {
            $order->visiblePrices();
            $order->visibleHumanPrices();

            $order->items->map(function ($item) {
                $item->prices = $this->orderItemUtil->getPrices($item);
                $item->human_prices = $this->orderItemUtil->getHumanPrices($item);

                return $item;
            });

            return $order;
        });

        return response()->json($invoice);
    }

    public function updateStatus($code)
    {
        if (!$this->request->has('status')) {
            return response()->json([
                'error' => 'Status invalid.'
            ]);
        }

        $invoice = Invoice::where('code', $code)->first();
        $invoice->status = $this->request->status;
        $invoice->save();

        $param = [
            'body' => 'Status order untuk '.$invoice->code.', berubah menjadi '.$invoice->status,
            'title' => 'Status order anda berubah'
        ];

        $data = [
            'invoice_id' => $code
        ];

        $this->sendNotification($param, $invoice->user_id, $data);

        return response()->json([
            'success' => 'Status has changed.'
        ]);
    }

    public function doPrint($code)
    {
        $invoice = Invoice::where('code', $code)->first();

        $terms_and_conditions = Setting::where('name', 'terms_and_conditions')->first();

        $page = [
            'title' => $invoice->code
        ];

        return view('admin.print.invoice_3', compact(
            'page',
            'invoice',
            'terms_and_conditions'
        ));
    }
}
