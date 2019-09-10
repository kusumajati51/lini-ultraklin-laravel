<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\V1\Invoice;

use App\Libraries\RequestLibrary;

use App\Traits\V1\UserTrait;

use App\Utils\InvoiceUtil;
use App\Utils\TimeUtil;

class InvoiceController extends Controller
{
    use UserTrait;

    protected $filter, $request;

    protected $invoiceUtil, $timeUtil;

    public function __construct(
        Request $request,
        InvoiceUtil $invoiceUtil,
        TimeUtil $timeUtil
    )
    {
        $this->request = $request;

        $this->invoiceUtil = $invoiceUtil;
        $this->timeUtil = $timeUtil;
    }

    public function createFilter()
    {
        $this->filter = [
            'sort' => ['invoices.id', 'desc'],
            'date' => [
                $this->timeUtil->createTimeRange(date('Y-m-d'))->start->toDateTimeString(),
                $this->timeUtil->createTimeRange(date('Y-m-d'))->end->toDateTimeString()
            ],
            'status' => ['Paid', 'Unpaid'],
            'user_status' => ['user'],
            'online' => 1,
            'region' => [],
            'codes' => []
        ];

        $requestLib = new RequestLibrary($this->filter, $this->request);

        if ($requestLib->has('date') && $requestLib->isArray('date') && $requestLib->hasSameCount('date', 2)) {
            $this->filter['date'] = [
                $this->timeUtil->createTimeRange($this->request->date[0], $this->request->date[1])->start->toDateTimeString(),
                $this->timeUtil->createTimeRange($this->request->date[0], $this->request->date[1])->end->toDateTimeString()
            ];
        }

        if ($requestLib->has('status') && $requestLib->in('status', ['Paid', 'Unpaid'])) {
            $this->filter['status'] = $this->request->status;
        }

        if ($requestLib->has('user_status') && $requestLib->in('user_status', ['user', 'tester', 'sales', 'agent', 'partner'])) {
            $this->filter['user_status'] = $this->request->user_status;
        }

        if ($requestLib->has('online') && $requestLib->in('online', [1, 0])) {
            $this->filter['online'] = $this->request->online;
        }

        if ($requestLib->has('region')) {
            $this->filter['region'] = $this->request->region;
        }

        if ($requestLib->has('codes')) {
            $this->filter['codes'] = $this->request->codes;
        }

        return $this->filter;
    }

    public function invoices()
    {
        $invoices = $this->invoiceUtil
            ->collect();

        if (!is_null($this->filter)) $invoices->filter($this->filter);

        $invoices->model()->with([
            'user' => function ($user) {
                $user->select([
                    'id', 'name', 'email', 'phone'
                ]);
            },
            'promotion' => function ($promotion) {
                $promotion->select([
                    'id', 'code', 'name'
                ]);
            },
            'orders' => function ($order) {
                $order->select([
                    'orders.*',
                    'packages.display_name as package',
                    'user_stores.name as store_name'
                ])
                ->leftJoin('packages', 'packages.id', 'orders.package_id')
                ->leftJoin('user_stores', 'user_stores.id', 'orders.store_id');
            }
        ]);

        if (auth('api')->check() && $this->request->segment(3) == 'stores') {
            $invoices->model()->whereHas('orders', function ($order) {
                $order->where('store_id', $this->user()->store->id);
            });
        }

        return $invoices;
    }

    public function index()
    {
        $this->createFilter();

        $invoices = $this->invoices();

        if (auth('api')->check() && $this->request->segment(3) == 'stores') {
            $invoices->model()->whereHas('orders', function ($order) {
                $order->where('store_id', $this->user()->store->id);
            });
        }

        $invoices = $invoices->paginate();

        $invoices->getCollection()->transform(function ($invoice) {
            $invoice->visibleNumber();
            $invoice->visibleCurrency();

            $invoice->orders->map(function ($order) {
                $order->visibleNumber();
                $order->visibleCurrency();
            });

            return $invoice;
        });

        $invoices->appends($this->filter);

        $data = $invoices->toArray();

        $data['filter'] = $this->filter;

        return response()->json($data);
    }

    public function getInvoice($code)
    {
        $invoice = $this->invoices()->find($code);

        if (is_null($invoice)) {
            return response()->json([
                'error' => 1,
                'message' => 'Invoice not found.'
            ], 400);
        }

        $invoice->visibleNumber();
        $invoice->visibleCurrency();

        $invoice->orders->map(function ($order) {
            $order->visibleNumber();
            $order->visibleCurrency();
        });

        return response()->json($invoice);
    }
}
