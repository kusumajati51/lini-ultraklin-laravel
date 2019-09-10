@extends('admin._master')

@section('content')
<div id="order-detail" class="uk-card uk-card-small uk-card-default">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header">
            <a class="uk--tool-header-back" href="{{ url('/admin/offline-orders') }}">
                <i class="fas fa-angle-left fa-lg"></i>
            </a>
            <h3 class="uk--tool-header-title">ORDER DETAIL</h3>
        </div>
    </div>
    <div class="uk-card-body">
        <div class="uk--box">
            <div class="uk--box-body-with-padding">
                <div class="uk-grid-collapse" uk-grid>
                    <div class="uk-width-2-5">
                        <span class="uk--box-label">Order Code</span>
                        <span class="uk--box-text">@{{ order.code }}</span>
                    </div>
                    <div class="uk-width-1-5">
                            <span class="uk--box-label">Package</span>
                            <span class="uk--box-text">@{{ (order.package) ? order.package.display_name : '' }}</span>
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
                        <template v-if="order.invoice && order.invoice.order_with_promotion != null && order.invoice.order_with_promotion.id == order.id">
                            <tr>
                                <td colspan="3">Discount</td>
                                <td class="uk-text-right">@{{ order.invoice.discount }}</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="uk-text-right">@{{ order.total_price - order.invoice.discount }}</td>
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
                        <el-button v-if="order.edit" type="danger" size="small" @click="cancelEditOrder">Cancel</el-button>
                        <el-button v-if="order.edit" type="success" size="small" :loading="order.editLoading" @click="updateOrder">Done</el-button>
                        <el-button v-else type="primary" size="small" @click="editOrder">Edit</el-button>
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
        el: '#order-detail',

        data: {
            order: {}
        },

        filters: {
            humanKey(val) {
                return val.replace(/(_)/g, ' ')
            }
        },

        methods: {
            getOrder() {
                axios.get(`${Laravel.url}/admin/orders/{{ $id }}`)
                    .then(({ data }) => {
                        this.order = data

                        this.$set(this.order, 'inputStatus', this.order.status)
                        this.$set(this.order, 'edit', false)
                        this.$set(this.order, 'editLoading', false)
                    })
                    .catch(error => {
                        this.$notify({
                            title: error.response.status,
                            message: error.response.statusText,
                            type: 'error'
                        })
                    })
            },
            editOrder() {
                this.order.edit = true
            },
            cancelEditOrder() {
                this.order.edit = false
            },
            updateOrder() {
                this.order.edit = false
                this.order.editLoading = true

                axios.post(`${Laravel.url}/admin/orders/${this.order.id}/change-status`, {
                    status: this.order.inputStatus
                })
                .then(({ data }) => {
                    this.order.editLoading = false

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

                    this.order.status = this.order.inputStatus
                })
                .catch(error => {
                    this.order.editLoading = false

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
            this.getOrder()
        }
    })
</script>
@endsection