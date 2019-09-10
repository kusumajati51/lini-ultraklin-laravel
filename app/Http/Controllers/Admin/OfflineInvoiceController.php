<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Invoice;

class OfflineInvoiceController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $invoices = Invoice::doesntHave('user')
            ->paginate(24);

        return view('admin.offline_invoice.index', compact(
            'invoices'
        ));
    }

    public function show($code)
    {
        $invoice = Invoice::where('code', $code)->first();

        if ($this->request->wantsJson()) {
            $invoice = Invoice::where('code', $code)
                ->with([
                    'user',
                    'promotion',
                    'orders.items',
                    'orders.package.service'
                ])
                ->first();

            return response()->json($invoice);
        }

        return view('admin.offline_invoice.show', compact(
            'code', 'invoice'
        ));
    }
}
