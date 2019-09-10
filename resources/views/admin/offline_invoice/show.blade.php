@extends('admin._master')

@section('content')
<div id="invoice-detail" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header">
            <a class="uk--tool-header-back" href="{{ url('/admin/offline-invoices') }}">
                <i class="fas fa-angle-left fa-lg"></i>
            </a>
            <h3 class="uk--tool-header-title">INVOICE DETAIL</h3>
        </div>
    </div>
    <div class="uk-card-body">
        <div class="uk--box">
            <div class="uk--box-header">
                <div class="uk-grid-collapse" uk-grid>
                    <div class="uk-width-2-5">
                        <span class="uk--box-label">Invoice No.</span>
                        <span class="uk--box-text">@{{ invoice.code }}</span>
                    </div>
                    <div class="uk-width-1-5">
                        <span class="uk--box-label">Customer</span>
                        <span class="uk--box-text">-</span>
                    </div>
                    <div class="uk-width-1-5">
                        <span class="uk--box-label">Status</span>
                        <span class="uk--box-text">@{{ invoice.status }}</span>
                    </div>
                    <div class="uk-width-1-5">
                        <span class="uk--box-label">Promo</span>
                        <span class="uk--box-text">@{{ (invoice.promotion) ? invoice.promotion.code : 'None' }}</span>
                    </div>
                </div>
            </div> <!-- invoice-box-header -->
            <div class="uk--box-body-with-padding">
                <div class="uk-grid-collapse" uk-grid>
                    <div class="uk-width-1-4">
                        <span class="uk--box-label">Payment</span>
                        <span class="uk--box-text">@{{ invoice.payment }}</span>
                    </div>
                    <div class="uk-width-1-4">
                        <span class="uk--box-label">Sub Total</span>
                        <span class="uk--box-text">@{{ invoice.total }}</span>
                    </div>
                    <div class="uk-width-1-4">
                        <span class="uk--box-label">Discount</span>
                        <span class="uk--box-text">@{{ invoice.discount }}</span>
                    </div>
                    <div class="uk-width-1-4">
                        <span class="uk--box-label">Total</span>
                        <span class="uk--box-text">@{{ invoice.total - invoice.discount }}</span>
                    </div>
                </div>
            </div> <!-- invoice-box-body -->
            <div class="uk--box-footer">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-expand@m">
                        <el-radio-group v-model="invoice.inputStatus" :disabled="!invoice.edit" size="small">
                            <el-radio-button label="Unpaid"></el-radio-button>
                            <el-radio-button label="Paid"></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="uk-width-auto@m">
                        <el-button v-if="invoice.edit" type="danger" size="small" @click="cancelEditInvoice">Cancel</el-button>
                        <el-button v-if="invoice.edit" type="success" size="small" :loading="invoice.editLoading" @click="updateInvoice">Done</el-button>
                        <el-button v-else type="primary" size="small" @click="editInvoice">Edit</el-button>
                    </div>
                </div>
            </div> <!-- invoice-box-footer -->
        </div> <!-- invoice-box -->

        <div v-for="(order, index) in invoice.orders" class="uk--box">
            <div class="uk--box-body-with-padding">
                <div class="uk-grid-collapse" uk-grid>
                    <div class="uk-width-2-5">
                        <span class="uk--box-label">Order Code</span>
                        <span class="uk--box-text">@{{ order.code }}</span>
                    </div>
                    <div class="uk-width-1-5">
                            <span class="uk--box-label">Package</span>
                            <span class="uk--box-text">@{{ order.package.display_name }}</span>
                        </div>
                    <div class="uk-width-1-5">
                        <span class="uk--box-label">Date</span>
                        <span class="uk--box-text">@{{ order.date }}</span>
                    </div>
                    <div class="uk-width-1-5">
                        <span class="uk--box-label">Status</span>
                        <span class="uk--box-text">@{{ order.status }}</span>
                    </div>
                </div>
                <div class="uk-grid-collapse uk-margin-top" uk-grid>
                    <div class="uk-width-2-5">
                        <span class="uk--box-label">Customer</span>
                        <span class="uk--box-text">@{{ (order.detail) ? order.detail.name : '' }} <small>(@{{ (order.detail) ? order.detail.phone : '' }})</small></span>
                    </div>
                    <div v-for="(val, key) in filteredDetail(order.detail)" class="uk-width-1-5">
                        <span class="uk--box-label">@{{ key | humanKey }}</span>
                        <span class="uk--box-text">@{{ val }}</span>
                    </div>  
                </div>
                <div class="uk-grid-collapse uk-margin-top" uk-grid>
                    <div class="uk-width-2-5">
                        <span class="uk--box-label">Location</span>
                        <span class="uk--box-text">@{{ order.location }}</span>
                    </div>
                </div>
                <table class="uk-table uk-table-small uk-table-divider">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="uk-text-right" width="100px">Price</th>
                            <th class="uk-text-right" width="100px">Quantity</th>
                            <th class="uk-text-right" width="100px">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in order.items">
                            <td>@{{ item.name }}</td>
                            <td class="uk-text-right">@{{ item.pivot.price }}</td>
                            <td class="uk-text-right">@{{ item.pivot.quantity }}</td>
                            <td class="uk-text-right">@{{ item.pivot.price * item.pivot.quantity }}</td>
                        </tr>
                    </tbody>
                    <tfooter>
                        <tr>
                            <td colspan="2">Total</td>
                            <td class="uk-text-right">@{{ order.total_quantity }}</td>
                            <td class="uk-text-right">@{{ order.total_price }}</td>
                        </tr>
                        <template v-if="invoice.order_with_promotion && invoice.order_with_promotion.id == order.id">
                            <tr>
                                <td colspan="3">Discount</td>
                                <td class="uk-text-right">@{{ invoice.discount }}</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="uk-text-right">@{{ order.total_price - invoice.discount }}</td>
                            </tr>
                        </template>
                    </tfooter>
                </table>
            </div>
            <div class="uk--box-footer">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-expand@m">
                        <el-radio-group v-model="order.inputStatus" :disabled="!order.edit" size="small">
                            <el-radio-button label="Cancel"></el-radio-button>
                            <el-radio-button label="Pending"></el-radio-button>
                            <el-radio-button label="Confirm"></el-radio-button>
                            <el-radio-button label="On The Way"></el-radio-button>
                            <el-radio-button label="Process"></el-radio-button>
                            <el-radio-button label="Done"></el-radio-button>
                        </el-radio-group>
                    </div>
                    {{--  <div class="uk-with-auto">
                        <el-autocomplete
                            class="inline-input"
                            placeholder="Input CSO"
                            :trigger-on-focus="false"
                            size="small"
                            :disabled="!order.edit"></el-autocomplete>
                    </div>  --}}
                    <div class="uk-width-auto@m">
                        <el-button v-if="order.edit" type="danger" size="small" @click="cancelEditOrder(index)">Cancel</el-button>
                        <el-button v-if="order.edit" type="success" size="small" :loading="order.editLoading" @click="updateOrder(index)">Done</el-button>
                        <el-button v-else type="primary" size="small" @click="editOrder(index)">Edit</el-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/js/lodash.min.js') }}"></script>
<script>
    new Vue({
        el: '#invoice-detail',

        data: {
            invoice: {}
        },

        filters: {
            humanKey(val) {
                return val.replace(/(_)/g, ' ')
            }
        },

        methods: {
            getInvoice() {
                axios.get(Laravel.url + '/admin/invoices/{{ $code }}')
                    .then(({ data }) => {
                        this.invoice = data

                        this.invoice.orders.forEach((order, index) => {
                            this.$set(this.invoice, 'inputStatus', this.invoice.status)
                            this.$set(this.invoice, 'edit', false);
                            this.$set(this.invoice, 'editLoading', false);

                            this.$set(this.invoice.orders[index], 'inputStatus', this.invoice.orders[index].status)
                            this.$set(this.invoice.orders[index], 'edit', false)
                            this.$set(this.invoice.orders[index], 'editLoading', false)
                        })
                    })
                    .catch(error => {
                        this.$notify({
                            title: error.response.status,
                            message: error.response.statusText,
                            type: 'error'
                        })
                    })
            },
            editInvoice() {
                this.invoice.edit = true
            },
            cancelEditInvoice() {
                this.invoice.edit = false
            },
            updateInvoice() {
                this.invoice.edit = false
                this.invoice.editLoading = true

                axios.post(`${Laravel.url}/admin/invoices/${this.invoice.code}/change-status`, {
                    _method: 'PATCH',
                    status: this.invoice.inputStatus
                })
                .then(({ data }) => {
                    this.invoice.editLoading = false

                    if (data.error) {
                        this.$notify({
                            title: 'Error',
                            message: data.error,
                            type: 'error'
                        })

                        return
                    }

                    this.$notify({
                        title: 'Success',
                        message: data.success,
                        type: 'success'
                    })

                    this.invoice.status = this.invoice.inputStatus
                })
                .catch(error => {
                    this.invoice.editLoading = false

                    this.$notify({
                        title: error.response.status,
                        message: error.response.statusText,
                        type: 'error'
                    })
                })
            },
            editOrder(index) {
                this.invoice.orders[index].edit = true
            },
            cancelEditOrder(index) {
                this.invoice.orders[index].edit = false
            },
            updateOrder(index) {
                this.invoice.orders[index].edit = false
                this.invoice.orders[index].editLoading = true

                let order = this.invoice.orders[index]

                axios.post(`${Laravel.url}/admin/orders/${order.id}/change-status`, {
                    _method: 'PATCH',
                    status: order.inputStatus
                })
                .then(({ data }) => {
                    this.invoice.orders[index].editLoading = false

                    if (data.error) {
                        this.$notify({
                            title: 'Error',
                            message: data.error,
                            type: 'error'
                        })

                        return
                    }

                    this.$notify({
                        title: 'Success',
                        message: data.success,
                        type: 'success'
                    })

                    this.invoice.orders[index].status = order.inputStatus
                })
                .catch(error => {
                    this.invoice.orders[index].editLoading = false

                    this.$notify({
                        title: error.response.status,
                        message: error.response.statusText,
                        type: 'error'
                    })
                })
            },
            filteredDetail(value) {
                let model = {
                    building_type: null,
                    cso_gender: null,
                    pets: null,
                    fragrance: null,
                    total_items: null

                }

                let detail = _.pick(value, _.keys(model))

                return detail
            }
        },

        mounted() {
            this.getInvoice()
        }
    })
</script>
@endsection