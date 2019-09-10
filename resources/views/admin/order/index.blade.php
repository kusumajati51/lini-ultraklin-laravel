@extends('admin._master')

@section('content')
<div id="page--order" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">ORDERS <sup class="el-badge__content">{{ $orders->total() }}</sup></h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <a href="{{ url('/admin/offline-orders/create') }}"><i class="fas fa-plus"></i></a>
                </div>
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
                <input type="hidden" name="date_mode" :value="filter.dateMode">
                <input v-for="item in filter.status" type="hidden" name="status[]" :value="item">
                <input v-for="item in filter.userStatus" type="hidden" name="user_status[]" :value="item">
                <input v-for="item in filter.orderSource" type="hidden" name="order_source[]" :value="item">
                <input v-for="item in filter.region" type="hidden" name="region[]" :value="item">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-expand">
                        <el-radio-group v-model="filter.dateMode" size="small">
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
                                placeholder="Pick a day"
                                size="small">
                            </el-date-picker>
                        </div>
                        <div>
                            <el-date-picker v-if="filter.dateMode == 'range'"
                                v-model="filter.daterange"
                                type="daterange"
                                name="daterange"
                                start-placeholder="Start date"
                                end-placeholder="End date"
                                size="small">
                            </el-date-picker>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-3">
                        <el-select v-model="filter.status" class="uk-width-1-1" placeholder="Status" multiple>
                            <el-option v-for="item in option.status"
                                :key="item.value"
                                :label="item.text"
                                :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-width-1-3">
                        <el-select v-model="filter.userStatus" class="uk-width-1-1" placeholder="User status" multiple>
                            <el-option v-for="item in option.userStatus"
                                :key="item.value"
                                :label="item.text"
                                :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-width-1-3">
                        <el-input v-model="filter.search" name="search" placeholder="Search">
                            <el-button slot="append" native-type="submit">
                                <i class="fas fa-search"></i>
                            </el-button>
                        </el-input>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-5">
                        <el-select v-model="filter.orderSource" class="uk-width-1-1" placeholder="Order source" multiple>
                            <el-option v-for="item in option.orderSource"
                                :key="item"
                                :label="item"
                                :value="item"></el-option>
                        </el-select>
                    </div>
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
                    <button class="el-button el-button--primary el-button--small">Filter</button>
                </div>
            </form>
            <hr>
        </div>
        <table class="uk-table uk-table-small uk-table-divider uk-text-small">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Package</th>
                    <th>Status</th>
                    <th>Referral</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ url('/admin/orders/'.$order->id) }}">{{ $order->code }}</a>
                        </td>
                        <td>{{ is_null($order->invoice->user) ? $order->invoice->customer->name : $order->invoice->user->name }}</td>
                        <td>{{ is_null($order->invoice->user) ? $order->invoice->customer->phone : $order->invoice->user->phone }}</td>
                        <td>{{ $order->package->display_name }}</td>
                        <td><el-tag :type="tagStatus['{{ $order->status }}']" size="mini">{{ $order->status }}</el-tag></td>
                        @if ($order->invoice->user_id)
                            <td>{{ $order->invoice->user->referral }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $orders->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var filter = {
        dateMode: '{{ $filter["date_mode"] }}',
        time: JSON.parse('{!! json_encode($filter["time"]) !!}'),
        status: JSON.parse('{!! json_encode($filter["status"]) !!}'),
        userStatus: JSON.parse('{!! json_encode($filter["user_status"]) !!}'),
        orderSource: JSON.parse('{!! json_encode($filter["order_source"]) !!}'),
        region: JSON.parse('{!! json_encode($filter["region"]) !!}')
    }

    new Vue({
        el: '#page--order',

        data: {
            filter: {
                active: true,
                dateMode: filter.dateMode,
                time: filter.time,
                date: Date.parse(filter.time[0]),
                daterange: [Date.parse(filter.time[0]), Date.parse(filter.time[1])],
                status: filter.status,
                userStatus: filter.userStatus,
                search: '',
                orderSource: filter.orderSource,
                region: filter.region
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
                region: JSON.parse('{!! json_encode($options["region"]) !!}'),
                orderSource: ['Online', 'Offline']
            },
            tagStatus: {
                'Paid': 'success',
                'Unpaid': 'danger',
                'Cancel': 'danger',
                'Pending': 'warning',
                'Confirm': '',
                'On The Way': '',
                'Process': '',
                'Done': 'success'
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