@extends('admin.print.__master')

@section('content')
<div class="uk-margin-top">
    <!-- HEADER -->
    <div class="uk--print-header uk-text-center">
        <h1 class="uk-margin-remove">UltraKlin</h1>
        <h3 class="uk-margin-remove">{{ $invoice->code }}</h3>
    </div>
    <!-- INVOICE -->
    <div class="uk-margin-medium-bottom">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-2-5">
                <span class="uk--box-label">Date</span>
                <span class="uk--box-text">{{ $invoice->created_at->toDateString() }}</span>
            </div>
            <div class="uk-width-1-5">
                <span class="uk--box-label">Customer</span>
                <span class="uk--box-text">{{ $invoice->user == null ? $invoice->customer->name : $invoice->user->name }}</span>
            </div>
            <div class="uk-width-1-5">
                <span class="uk--box-label">Status</span>
                <span class="uk--box-text">{{ $invoice->status }}</span>
            </div>
            <div class="uk-width-1-5">
                <span class="uk--box-label">Promo</span>
                <span class="uk--box-text">{{ $invoice->promotion ? $invoice->promotion->code : 'None' }}</span>
            </div>
        </div>
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-2-5">
                <span class="uk--box-label">Payment</span>
                <span class="uk--box-text">{{ $invoice->payment }}</span>
            </div>
            <div class="uk-width-1-5">
                <span class="uk--box-label">Sub Total</span>
                <span class="uk--box-text">{{ number_format($invoice->total, 0, ',', '.') }}</span>
            </div>
            <div class="uk-width-1-5">
                <span class="uk--box-label">Discount</span>
                <span class="uk--box-text">{{ number_format($invoice->discount, 0, ',', '.') }}</span>
            </div>
            <div class="uk-width-1-5">
                <span class="uk--box-label">Total</span>
                <span class="uk--box-text">{{ number_format($invoice->total - $invoice->discount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <!-- ORDERS -->
    <div class="uk-margin">
        <table class="uk-table uk-table-divider uk-table-small">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Date</th>
                    <th>Item's</th>
                    <th class="uk-text-right">Price</th>
                    <th class="uk-text-right">Extra Price</th>
                    <th class="uk-text-right">Discount</th>
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
                        <td>{{ $order->date }}</td>
                        <td>{{ $order->items->count() }} item's</td>
                        <td class="uk-text-right">{{ number_format($order->total_price_item, 0, ',', '.') }}</td>
                        <td class="uk-text-right">
                            @if ($order->additional_cso > 0)
                                {{ number_format($order->total_price_item * $order->additional_cso, 0, ',', '.') }}
                            @else
                                0
                            @endif
                        </td>
                        <td class="uk-text-right">
                            @if ($invoice->order_with_promotion != null && $invoice->order_with_promotion->id == $order->id)
                                {{ number_format($invoice->discount, 0, ',', '.') }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
