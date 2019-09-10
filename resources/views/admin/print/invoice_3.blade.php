@extends('admin.print.__master_2')

@section('content')
    @foreach ($invoice->orders as $order)
        <div class="page-container">
            <div class="uk-grid uk-grid-small" uk-grid>
                <div class="uk-width-1-2">
                    <div>UltraKlin</div>
                    <div>{{ $invoice->code }}</div>
                    <div class="uk-margin-small">{{ $order->region->address }}</div>
                    <div class="uk-margin-small">{{ $order->region->phone }}</div>
                </div>
                <div class="uk-width-1-2">
                    <div class="uk-grid uk-grid-small" uk-grid>
                        <div class="uk-margin-remove uk-width-1-4">Customer</div>
                        <div class="uk-margin-remove uk-width-3-4 uk-text-uppercase">: {{ $invoice->user == null ? $invoice->customer->name : $invoice->user->name }}</div>
                        <div class="uk-margin-remove uk-width-1-4">Phone</div>
                        <div class="uk-margin-remove uk-width-3-4">: {{ $invoice->user == null ? $invoice->customer->phone : $invoice->user->phone }}</div>
                        <div class="uk-margin-remove uk-width-1-4">Date</div>
                        <div class="uk-margin-remove uk-width-3-4">: {{ $invoice->created_at->toDateString() }}</div>
                        <div class="uk-margin-remove uk-width-1-4">Payment</div>
                        <div class="uk-margin-remove uk-width-3-4">: {{ $invoice->payment }}</div>
                        <div class="uk-margin-remove uk-width-1-4">Status</div>
                        <div class="uk-margin-remove uk-width-3-4">: {{ $invoice->status }}</div>
                    </div>
                </div>
            </div>
            <div class="page-divider"></div>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-4">
                    <div>Order Time</div>
                    <div>
                        {{ $order->date }}
                    </div>
                </div>
                <div class="uk-width-1-4">
                    <div>Delivery Time</div>
                    <div>
                        @if (isset($order->detail['delivery_date']))
                            {{ $order->detail['delivery_date'] }}
                        @endif
                    </div>
                </div>
                <div class="uk-width-1-2">
                    <div>Address</div>
                    <div>
                        {{ $order->location }}
                    </div>
                </div>
                <div class="uk-width-1-4">
                    <div>Code</div>
                    <div>
                        {{ $order->code }}
                    </div>
                </div>
                <div class="uk-width-1-4">
                    <div>Package</div>
                    <div>
                        {{ $order->package->display_name }}
                    </div>
                </div>
                <div class="uk-width-1-2">
                    <div>Note</div>
                    <div>
                        {{ $order->note }}
                    </div>
                </div>
            </div>
            <div class="page-divider"></div>
            <table class="page-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="uk-text-right" width="100">Quantity</th>
                        <th class="uk-text-right" width="150">Price</th>
                        <th class="uk-text-right" width="150">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->name }}
                            </td>
                            <td class="uk-text-right">
                                {{ number_format($item->pivot->quantity, 0, ',', '.') }}
                            </td>
                            <td class="uk-text-right">
                                {{ $item->human_prices->price }}
                            </td>
                            <td class="uk-text-right">
                                {{ $item->human_prices->sub_total }}
                            </td>
                        </tr>
                    @endforeach

                    @if (isset($order->detail['total_cso']))
                        <tr>
                            <td>Additional CSO</td>
                            <td class="uk-text-right">{{ number_format($order->additional_cso, 0, ',', '.') }}</td>
                            <td class="uk-text-right">{{ $order->human_prices->total }}</td>
                            <td class="uk-text-right">{{ $order->human_prices->extra_price_cso }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="page-footer">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-4">
                        <div class="uk-text-center">
                            <div>Hormat Kami</div>
                            <div class="page-signature"></div>
                            <div>({{ auth('officer')->user()->name }})</div>
                        </div>
                    </div>
                    <div class="uk-width-1-2">
                        @if (isset($terms_and_conditions))
                            <ol>
                                @foreach ($terms_and_conditions->value as $val)
                                    <li>{{ $val }}</li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                    <div class="uk-width-1-4">
                        <table class="page-table">
                            <tbody>
                                <tr>
                                    <td colspan="3">Sub Total</td>
                                    <td class="uk-text-right">{{ $order->human_prices->sub_total }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">Discount</td>
                                    <td class="uk-text-right">{{ $order->human_prices->discount }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td class="uk-text-right">{{ $order->human_prices->final_total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@stop