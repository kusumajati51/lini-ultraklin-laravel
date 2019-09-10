<?php

namespace App\Http\Controllers\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            'status' => ['Paid', 'Unpad'],
            'user_status' => ['user'],
            'online' => 1,
            'region' => $this->user()->regions->pluck('code'),
            'codes' => []
        ];

        if ($this->request->has('codes')) {
            $this->filter['codes'] = $this->request->codes;
        }

        return $this->filter;
    }

    public function index()
    {
        $this->createFilter();

        $invoices = $this->invoiceUtil
            ->collect()
            ->filter($this->filter);

        $invoices->model()->with([
            'promotion',
            'orders' => function ($order) {
                $order->with([
                    'package'
                ]);
            }
        ]);

        $invoices = $invoices->paginate();

        $invoices->getCollection()->transform(function ($invoice) {
            $invoice->user = $invoice->getUser();
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
}
