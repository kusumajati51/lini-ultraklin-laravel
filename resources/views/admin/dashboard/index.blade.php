@extends('admin._master')

@section('content')
<div id="page--dashboard">
    <div class="uk-card uk-card-default uk-card-small uk-margin">
        <div class="uk-card-header">
            <div class="">User order</div>
        </div>
        <div class="uk-card-body">
            <div class="uk-grid uk-grid-small uk-child-width-1-4" uk-grid>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">Today</div>
                        <div class="uk--box-number">{{ $order['today'] }}</div>
                    </div>
                </div>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">Yesterday</div>
                        <div class="uk--box-number">{{ $order['yesterday'] }}</div>
                    </div>
                </div>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">Last 7 days</div>
                        <div class="uk--box-number">{{ $order['last_seven_days'] }}</div>
                    </div>
                </div>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">This Month</div>
                        <div class="uk--box-number">{{ $order['this_month'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="uk-card uk-card-default uk-card-small uk-margin">
        <div class="uk-card-header">
            <div>Registered user</div>
        </div>
        <div class="uk-card-body">
            <div class="uk-grid uk-grid-small uk-child-width-1-4" uk-grid>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">Today</div>
                        <div class="uk--box-number">{{ $user['today'] }}</div>
                    </div>
                </div>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">Yesterday</div>
                        <div class="uk--box-number">{{ $user['yesterday'] }}</div>
                    </div>
                </div>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">Last 7 days</div>
                        <div class="uk--box-number">{{ $user['last_seven_days'] }}</div>
                    </div>
                </div>
                <div>
                    <div class="uk--box no-border">
                        <div class="uk--box-label">This Month</div>
                        <div class="uk--box-number">{{ $user['this_month'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div>
        <div class="uk-card uk-card-default uk-card-small">
            <div class="uk-card-header">
                <div class="uk-grid uk-grid-small" uk-grid>
                    <div class="uk-width-expand">Income</div>
                    <div class="uk-width-auto">
                        <el-date-picker
                            v-model="incomeChart.filter.date"
                            type="month"
                            name="date"
                            value-format="yyyy-MM"
                            placeholder="Pick a day"
                            size="small"
                            @change="refreshIncomeChart">
                        </el-date-picker>
                    </div>
                </div>
            </div>
            <div class="uk-card-body">
                <canvas id="income-chart"></canvas>
            </div>
            <div class="uk-card-footer">
                <div class="uk-grid-small uk-child-width-1-3" uk-grid>
                    <div>
                        <div class="uk--box no-border">
                            <div class="uk--box-label">Total</div>
                            <div class="uk--box-number">@{{ incomeChart.paid + incomeChart.unpaid  | currency }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="uk--box no-border">
                            <div class="uk--box-label">Paid</div>
                            <div class="uk--box-number">@{{ incomeChart.paid  | currency }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="uk--box no-border">
                            <div class="uk--box-label">Unpaid</div>
                            <div class="uk--box-number">@{{ incomeChart.unpaid  | currency }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script src="{{ asset('js/lodash.min.js') }}"></script>
<script>
    new Vue({
        el: '#page--dashboard',

        data: {
            incomeChart: {
                filter: {
                    date: '{{ Carbon\Carbon::today()->toDateString() }}'
                },
                paid: 0,
                unpaid: 0,
                chart: null
            }
        },

        methods: {
            createChart(id, data) {
                const ctx = document.getElementById(id).getContext('2d')

                if (this.incomeChart.chart != null) {
                    this.incomeChart.chart.destroy()
                }

                this.incomeChart.chart = new Chart.Line(ctx, {
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: data.options
                })
            },
            getIncomeData(fn) {
                axios.get(`${Laravel.url}/admin/json/dashboard/income`, {
                    params: {
                        date: this.incomeChart.filter.date
                    }
                }).then(({ data }) => {
                    this.incomeChart.paid = _.sum(data.data.paid)
                    this.incomeChart.unpaid = _.sum(data.data.unpaid)

                    let labels = data.labels

                    let datasets = [
                        {
                            label: 'PAID',
                            backgroundColor: 'rgba(77,182,172 ,0.5)',
                            borderColor: 'rgba(77,182,172 ,1)',
                            data: data.data.paid,
                            fill: false
                        },
                        {
                            label: 'UNPAID',
                            backgroundColor: 'rgba(229,115,115 ,0.5)',
                            borderColor: 'rgba(229,115,115 ,1)',
                            data: data.data.unpaid,
                            hidden: true,
                            fill: false
                        }
                    ]

                    let options = {
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                                }
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    callback: function (value, index, values) {
                                        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                                    }
                                }
                            }]
                        }
                    }

                    return fn({
                        labels, datasets, options
                    })
                })
            },
            refreshIncomeChart() {
                this.getIncomeData((data) => {
                    this.createChart('income-chart', data)
                })
            }
        },

        mounted() {
            this.refreshIncomeChart()
        },

        filters: {
            currency(val) {
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
        }
    })
</script>
@stop