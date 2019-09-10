@extends('admin._master_no_sidebar')

@section('content')
<div id="content" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <a href="{{ url('/admin') }}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">ORDER REPORT<sup class="el-badge__content">{{ $orders->count() }}</sup></h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <a class="uk-link-reset" href="#" @click.prevent="activeFilter">
                        <i class="fas fa-filter"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <div v-if="filter.active">
            <form method="GET">
                <input v-for="item in filter.region" type="hidden" name="region[]" :value="item">
                <input type="hidden" name="date_mode" :value="filter.dateMode">
                <input v-for="item in filter.status" type="hidden" name="status[]" :value="item">
                <input v-for="item in filter.userStatus" type="hidden" name="user_status[]" :value="item">
                <input v-for="item in filter.paymentStatus" type="hidden" name="payment_status[]" :value="item">
                <input v-for="item in filter.orderSource" type="hidden" name="order_source[]" :value="item">
                <input type="hidden" name="service" :value="filter.service">

                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-expand">
                        <el-radio-group v-model="filter.dateMode">
                            <el-radio-button label="daily">Daily</el-radio-button>
                            <el-radio-button label="range">Range</el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="uk-width-auto">
                        <div>
                            <el-date-picker v-if="filter.dateMode == 'daily'"
                                v-model="filter.date"
                                type="date"
                                name="date"
                                placeholder="Pick a day">
                            </el-date-picker>
                        </div>
                        <div>
                            <el-date-picker v-if="filter.dateMode == 'range'"
                                v-model="filter.daterange"
                                type="daterange"
                                name="daterange"
                                start-placeholder="Start date"
                                end-placeholder="End date">
                            </el-date-picker>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-3-5">
                        <el-select v-model="filter.status" class="uk-width-1-1" placeholder="Status" multiple>
                            <el-option v-for="item in option.status"
                                :key="item.value"
                                :label="item.text"
                                :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-width-1-5">
                        <el-select v-model="filter.userStatus" class="uk-width-1-1" placeholder="User status" multiple>
                            <el-option v-for="item in option.userStatus"
                                :key="item.value"
                                :label="item.text"
                                :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-width-1-5">
                        <el-select v-model="filter.paymentStatus" class="uk-width-1-1" placeholder="Payment status" multiple>
                            <el-option v-for="item in option.paymentStatus"
                                :key="item"
                                :label="item"
                                :value="item"></el-option>
                        </el-select>
                    </div>
                </div>
                <div class="uk-margin uk-grid uk-grid-small" uk-grid>
                    <div class="uk-width-1-5">
                        <el-select v-model="filter.orderSource" class="uk-width-1-1" placeholder="Order Source" multiple>
                            <el-option v-for="item in option.orderSource"
                                :key="item"
                                :label="item"
                                :value="item"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-width-1-5">
                        <el-select v-model="filter.service" class="uk-width-1-1" placeholder="Service">
                            <el-option v-for="item in option.service"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-with-auto uk-flex uk-flex-middle">
                        <label class="uk-form-label">
                            <input v-model="filter.sum" class="uk-checkbox" type="checkbox">
                            <span class="uk-margin-small-left">Sum Date Range</span>
                        </label>
                    </div>
                </div>
                <div class="uk-margin uk-grid uk-grid-small" uk-grid>
                    <div class="uk-width-expand">
                        <el-select v-model="filter.region" class="uk-width-1-1" placeholder="Region" multiple>
                            <el-option v-for="item in option.region"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"></el-option>
                        </el-select>
                    </div>
                </div>
                <div class="uk-margin uk-text-right">
                    <a class="el-button el-button--success el-button--small" href="{{ request()->fullUrl() }}{{ request()->query() ? '&export=xlsx' : '?export=xlsx'  }}">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <button class="el-button el-button--primary el-button--small">Filter</button>
                </div>
            </form>
            <hr>
        </div>
        <table v-if="!filter.sum" class="uk-table uk-table-small uk-table-divider uk-text-small">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Package</th>
                    <th>Promo</th>
                    <th class="uk-text-right">Total</th>
                    <th class="uk-text-right">Discount</th>
                    <th class="uk-text-right">Final Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order['date'] }}</td>
                        <td>
                            @if ($order['payment_status'] == 'Paid')
                                <span class="uk-text-success">{{ $order['code'] }}</span>
                            @else
                                <span class="uk-text-danger">{{ $order['code'] }}</span>
                            @endif
                        </td>
                        <td>{{ $order['customer'] }}</td>
                        <td>{{ $order['package_name'] }}</td>
                        <td>{{ $order['promotion_code'] }}</td>
                        <td class="uk-text-right">{{ $order['human_prices']['total'] }}</td>
                        <td class="uk-text-right">{{ $order['human_prices']['discount'] }}</td>
                        <td class="uk-text-right">{{ $order['human_prices']['final_total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table v-else class="uk-table uk-table-divider uk-table-small uk-text-small">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="uk-text-right">Total</th>
                    <th class="uk-text-right">Discount</th>
                    <th class="uk-text-right">Final Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sumedOrders as $order)
                    <tr>
                        <td>{{ $order['date'] }}</td>
                        <td class="uk-text-right">{{ $order['human_prices']['total'] }}</td>
                        <td class="uk-text-right">{{ $order['human_prices']['discount'] }}</td>
                        <td class="uk-text-right">{{ $order['human_prices']['final_total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="uk-card-footer">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3 uk-text-center">
                <div class="uk--box-label">TOTAL</div>
                <h3 class="uk-margin-small">
                    {{ human_price($orders->sum('prices.sub_total')) }}
                </h3>
            </div>
            <div class="uk-width-1-3 uk-text-center">
                <div class="uk--box-label">DISCOUNT</div>
                <h3 class="uk-margin-small">
                    {{ human_price($orders->sum('prices.discount')) }}
                </h3>
            </div>
            <div class="uk-width-1-3 uk-text-center">
                <div class="uk--box-label">FINAL TOTAL</div>
                <h3 class="uk-margin-small">
                    {{ human_price($orders->sum('prices.final_total')) }}
                </h3>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var filter = {
        region: JSON.parse('{!! json_encode($filter["region"]) !!}'),
        dateMode: '{{ $filter["date_mode"] }}',
        time: JSON.parse('{!! json_encode($filter["time"]) !!}'),
        status: JSON.parse('{!! json_encode($filter["status"]) !!}'),
        userStatus: JSON.parse('{!! json_encode($filter["user_status"]) !!}'),
        paymentStatus: JSON.parse('{!! json_encode($filter["payment_status"]) !!}'),
        orderSource: JSON.parse('{!! json_encode($filter["order_source"]) !!}'),
        service: '{{ $filter["service"] }}'
    }

    new Vue({
        el: '#content',

        data: {
            filter: {
                active: true,
                sum: false,
                region: filter.region,
                dateMode: filter.dateMode,
                time: filter.time,
                date: Date.parse(filter.time[0]),
                daterange: [Date.parse(filter.time[0]), Date.parse(filter.time[1])],
                status: filter.status,
                userStatus: filter.userStatus,
                paymentStatus: filter.paymentStatus,
                orderSource: filter.orderSource,
                service: filter.service,
                search: ''
            },
            option: {
                status: [
                    {
                        value: 'Cancel',
                        text: 'Cancel'
                    },
                    {
                        value: 'Pending',
                        text: 'Pending'
                    },
                    {
                        value: 'Confirm',
                        text: 'Confirm'
                    },
                    {
                        value: 'On The Way',
                        text: 'On The Way'
                    },
                    {
                        value: 'Process',
                        text: 'Process'
                    },
                    {
                        value: 'Done',
                        text: 'Done'
                    }
                ],
                userStatus: [
                    {
                        value: 'user',
                        text: 'User'
                    },
                    {
                        value: 'sales',
                        text: 'Sales'
                    },
                    {
                        value: 'agent',
                        text: 'Agent'
                    },
                    {
                        value: 'tester',
                        text: 'Tester'
                    }
                ],
                paymentStatus: ['Paid', 'Unpaid'],
                orderSource: ['Online', 'Offline'],
                region: JSON.parse('{!! json_encode($options["region"]) !!}'),
                service: JSON.parse('{!! json_encode($options["service"]) !!}')
            }
        },

        methods: {
            activeFilter() {
                this.filter.active = !this.filter.active
            }
        }
    })
</script>
@endsection