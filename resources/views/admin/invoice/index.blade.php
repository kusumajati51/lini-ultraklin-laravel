@extends('admin._master') 
@section('content')
<div id="page--invoice" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">INVOICES <sup class="el-badge__content">{{ $invoices->total() }}</sup></h3>
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
                <input type="hidden" name="online" :value="filter.online">
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
                            <el-date-picker v-if="filter.dateMode == 'daily'" v-model="filter.date" type="date" name="date" placeholder="Pick a day"
                                size="small">
                            </el-date-picker>
                        </div>
                        <div>
                            <el-date-picker v-if="filter.dateMode == 'range'" v-model="filter.daterange" type="daterange" name="daterange" start-placeholder="Start date"
                                end-placeholder="End date" size="small">
                            </el-date-picker>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-3">
                        <el-select v-model="filter.status" class="uk-width-1-1" placeholder="Status" multiple>
                            <el-option v-for="item in option.status" :key="item.value" :label="item.text" :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-width-1-3">
                        <el-select v-model="filter.userStatus" class="uk-width-1-1" placeholder="User status" multiple>
                            <el-option v-for="item in option.userStatus" :key="item.value" :label="item.text" :value="item.value"></el-option>
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
                    <div class="uk-width-auto">
                        <el-radio-group v-model="filter.online">
                            <el-radio-button label="1">Online</el-radio-button>
                            <el-radio-button label="0">Offline</el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="uk-width-expand">
                        <el-select v-model="filter.region" class="uk-width-1-1" placeholder="Region" multiple>
                            <el-option v-for="item in option.region" :key="item.value" :label="item.label" :value="item.value"></el-option>
                        </el-select>
                    </div>
                </div>
                <div class="uk-margin uk-text-right">
                    <button class="el-button el-button--primary el-button--small">Filter</button>
                </div>
            </form>
            <hr>
        </div>
        @foreach ($invoices as $invoice)
        <div class="uk--box">
            <div class="uk--box-header">
                <div class="uk-grid-collapse" uk-grid>
                    <div class="uk-width-2-5">
                        <dl class="uk-description-list">
                            <dt>Invoice No.</dt>
                            <dd>
                                <a href="{{ url('/admin/invoices/'.$invoice->code) }}">{{ $invoice->code }}</a>
                            </dd>
                        </dl>
                    </div>
                    <div class="uk-width-1-5">
                        <dl class="uk-description-list">
                            <dt>Customer</dt>
                            <dd>
                                <a href="">{{ is_null($invoice->user) ? $invoice->customer->name : $invoice->user->name }}</a>
                            </dd>
                        </dl>
                    </div>
                    <div class="uk-width-1-5">
                        <dl class="uk-description-list">
                            <dt>Status</dt>
                            <dd>
                                <el-tag :type="tagStatus['{{ $invoice->status }}']" size="mini">{{ $invoice->status }}</el-tag>
                            </dd>
                        </dl>
                    </div>
                    <div class="uk-width-1-5">
                        <dl class="uk-description-list">
                            <dt>Promo</dt>
                            <dd>{{ (is_null($invoice->promotion)) ? 'NONE' : $invoice->promotion->code }}</dd>
                        </dl>
                    </div>
                </div> <!-- invoice-box-header -->
            </div>
            <div class="uk--box-body">
                <table class="uk-table uk-table-small uk-table-divider">
                    <tbody>
                        @foreach ($invoice->orders as $order)
                            <tr>
                                <td>{{ $order->package->display_name }}</td>
                                <td>{{ $order->date }}</td>
                                <td><el-tag :type="tagStatus['{{ $order->status }}']" size="mini">{{ $order->status }}</el-tag></td>
                                <td>{{ $order->items->count() }} item(s)</td>
                                <td>{{ $order->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> <!-- invoice-box-body -->
            <div class="uk--box-footer">
                <div class="uk-grid-collapse" uk-grid>
                    <div class="uk-width-1-4">
                        <dl class="uk-description-list">
                            <dt>Payment</dt>
                            <dd>{{ $invoice->payment }}</dd>
                            @if ($invoice->payment === 'Transfer')
                                <dt>Via</dt>
                                <dd>{{ $invoice->payments->bank }}</dd>
                            @endif
                        </dl>
                    </div>
                    <div class="uk-width-1-4">
                        <dl class="uk-description-list">
                            <dt>Sub Total</dt>
                            <dd>{{ $invoice->total }}</dd>
                        </dl>
                    </div>
                    <div class="uk-width-1-4">
                        <dl class="uk-description-list">
                            <dt>Discount</dt>
                            <dd>{{ $invoice->discount }}</dd>
                        </dl>
                    </div>
                    <div class="uk-width-1-4">
                        <dl class="uk-description-list">
                            <dt>Total</dt>
                            <dd>{{ $invoice->final_total }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <!-- invoice-box-footer -->
        </div>
        <!-- invoice-box -->
        @endforeach
        <div class="uk-margin-top uk-text-center">
            {{ $invoices->links('vendor.pagination.element-ui') }}
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
        online: parseInt('{{ $filter["online"] }}'),
        region: JSON.parse('{!! json_encode($filter["region"]) !!}')
    }

    new Vue({
        el: '#page--invoice',

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
                online: filter.online,
                region: filter.region
            },
            option: {
                status: [
                    {
                        value: 'Paid',
                        text: 'Paid'
                    },
                    {
                        value: 'Unpaid',
                        text: 'Unpaid'
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
                region: JSON.parse('{!! json_encode($options["region"]) !!}')
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