<?php

namespace App\Http\Controllers\SystemTool;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\V1\Invoice;

class InvoiceController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {

    }

    public function updatePrice($code)
    {
        $invoice = Invoice::where('code', $code)
            ->first();

        if (is_null($invoice)) {
            return response()->json([
                'error' => 1,
                'message' => 'Data not found.'
            ], 404);
        }

        $invoice->total = $invoice->orders->sum(function ($order) {
            return $order->prices->sub_total;
        });

        $invoice->save();

        return response()->json([
            'success' => 1,
            'data' => $invoice
        ]);
    }
}
