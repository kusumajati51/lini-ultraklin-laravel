@extends('admin.print.__master')

@section('content')
<div class="uk-margin-top">
    <!-- HEADER -->
    <!-- INVOICE -->
    <div class="uk--print-header">
        <div class="uk-grid uk-grid-small" uk-grid>
            <div class="uk-width-1-2">
                <h4 class="uk-margin-remove">UltraKlin</h4>
                <h4 class="uk-margin-remove">{{ $invoice->code }}</h4>
            </div>
            <div class="uk-width-1-2">
                <h4 class="uk-margin-remove">{{ $invoice->user == null ? $invoice->customer->name : $invoice->user->name }}</h4>
                <h4 class="uk-margin-remove">{{ $invoice->user == null ? $invoice->customer->phone : $invoice->user->phone }}</h4>
            </div>
        </div>
    </div>
    <div class="uk--print-header">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-1-4">
                <span class="uk--box-label">Date</span>
                <span class="uk--box-text">{{ $invoice->created_at->toDateString() }}</span>
            </div>
            <div class="uk-width-1-4">
                <span class="uk--box-label">Date</span>
                <span class="uk--box-text">{{ $invoice->payment }}</span>
            </div>
            <div class="uk-width-1-4">
                <span class="uk--box-label">Status</span>
                <span class="uk--box-text">{{ $invoice->status }}</span>
            </div>
            <div class="uk-width-1-4">
                <span class="uk--box-label">Promo</span>
                <span class="uk--box-text">{{ $invoice->promotion ? $invoice->promotion->code : 'None' }}</span>
            </div>
        </div>
    </div>
    <!-- ORDERS -->
    <div>
        <table class="uk-table uk-table-small">
            <thead>
                <tr>
                    <th>Package</th>
                    <th class="uk-text-right">Price</th>
                    <th class="uk-text-right">Discount</th>
                    <th class="uk-text-right">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->orders as $order)
                    <tr>
                        <td>
                            {{ $order->package->display_name }}
                            @if ($order->additional_cso > 0)
                                <small>(+{{ $order->additional_cso }} CSO)</small>
                            @endif
                        </td>
                        <td class="uk-text-right">{{ number_format($order->total_price_item + $order->total_price_additional_cso, 0, ',', '.') }}</td>
                        <td class="uk-text-right">
                            @if ($invoice->order_with_promotion != null && $invoice->order_with_promotion->id == $order->id)
                                {{ number_format($invoice->discount, 0, ',', '.') }}
                            @else
                                0
                            @endif
                        </td>
                        <td class="uk-text-right">{{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="uk-text-bold" colspan="3">TOTAL</td>
                    <td class="uk-text-right uk-text-bold">{{ number_format($invoice->total - $invoice->discount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@stop
