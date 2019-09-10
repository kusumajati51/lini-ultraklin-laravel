<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

use App\Invoice;
use App\Traits\V1\PaymentTrait;

class InvoiceController extends Controller
{
    use PaymentTrait;

    protected $filter;
    protected $request;
    protected $user;

    public function __construct(Request $request)
    {
        $this->filter = [
            'sort' => ['created_at', 'desc']
        ];

        $this->request = $request;
    }

    public function index()
    {
        $this->user = $this->request->user();

        $invoices = $this->user->invoices()
            ->with(['orders', 'payments']);

        if ($this->request->has('status')) {
            $this->filter = array_add($this->filter, 'status', $this->request->status);

            $invoices = $invoices->where(DB::raw('LOWER(status)'), strtolower($this->request->status));
        }

        if ($this->request->has('sort')) {
            $this->filter = array_set($this->filter, 'sort', $this->request->sort);
        }

        $invoices = $invoices
            ->orderBy($this->filter['sort'][0], $this->filter['sort'][1])
            ->paginate(24);

        $invoices->appends($this->filter);

        return response()->json($invoices);
    }

    public function show($code)
    {
        $this->user = $this->request->user();

        $invoice = $this->user->invoices()
            ->where('code', $code)
            ->with([
                'orders.ratings', 
                'promotion', 
                'payments'
            ])
            ->first();

        if (is_null($invoice)) {
            return response()->json([
                'error' => 'Invoice not found.'
            ]);
        } else if (!is_null($invoice->payments)) {
            $status = $this->getPaymentStatus($invoice->payments);
            $invoice->payments->setAttribute('detail_status', $status);
        }

        return response()->json($invoice);
    }
}
