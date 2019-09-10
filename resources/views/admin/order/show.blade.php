@extends('admin._master')

@section('content')
<div id="content">
    <div v-if="Object.keys(order).length > 0" class="uk-card uk-card-small uk-card-default">
        <div class="uk-card-header uk-padding-remove">
            <div class="uk--tool-header uk-grid-collapse" uk-grid>
                <div class="uk-width-auto">
                    <div class="uk--tool-header-icon">
                        <a class="uk--tool-header-back" href="{{ url()->previous() }}">
                            <i class="fas fa-angle-left fa-lg"></i>
                        </a>
                    </div>
                </div>
                <div class="uk-width-expand">
                    <h3 class="uk--tool-header-title">ORDER DETAIL</h3>
                </div>
                <div v-if="order.invoice" class="uk-width-auto">
                    <div class="uk--tool-header-icon">
                        <a :href="Laravel.url + '/admin/invoices/'+ order.invoice.code">
                            <i class="fas fa-file-alt"></i>
                        </a>
                    </div>
                </div>
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
                            <span class="uk--box-text"><el-tag :type="tagStatus[order.status]" size="mini">@{{ order.status }}</el-tag></span>
                        </div>
                    </div>
                    <div class="uk-grid-collapse uk-margin-top" uk-grid>
                        <div class="uk-width-3-5">
                            <span class="uk--box-label">Location</span>
                            <span class="uk--box-text">@{{ order.location }}</span>
                        </div>
                        <div v-if="order.invoice" class="uk-width-1-5">
                            <span class="uk--box-label">Customer</span>
                            <span class="uk--box-text">@{{ order.user.name }}</span>
                        </div>
                        <div v-if="order.invoice" class="uk-width-1-5">
                            <span class="uk--box-label">Phone</span>
                            <span class="uk--box-text">@{{ order.user.phone }}</span>
                        </div>
                    </div>
                    <div v-if="order.detail" class="uk-grid-collapse uk-margin-top" uk-grid>
                        <!-- CLEANING -->
                        <div v-if="order.detail.building_type" class="uk-width-1-5">
                            <span class="uk--box-label">Building Type</span>
                            <span class="uk--box-text">@{{ order.detail.building_type }}</span>
                        </div>
                        <div v-if="order.detail.pets" class="uk-width-1-5">
                            <span class="uk--box-label">Pets</span>
                            <span class="uk--box-text">@{{ order.detail.pets }}</span>
                        </div>
                        <div v-if="order.detail.cso_gender" class="uk-width-1-5">
                            <span class="uk--box-label">CSO Gender</span>
                            <span class="uk--box-text">@{{ order.detail.cso_gender }}</span>
                        </div>
                        <div v-if="order.detail.total_cso" class="uk-width-1-5">
                            <span class="uk--box-label">Total CSO</span>
                            <span class="uk--box-text">@{{ order.detail.total_cso }}</span>
                        </div>
                        <div v-if="order.detail.room" class="uk-width-1-5">
                            <span class="uk--box-label">Room</span>
                            <span class="uk--box-text">
                                <ul class="uk-list uk-margin-remove">
                                    <li class="uk-margin-remove" v-for="room in order.detail.room">@{{ room }}</li>
                                </ul>
                            </span>
                        </div>
                        <!-- LAUNDRY -->
                        <div v-if="order.detail.fragrance" class="uk-width-1-5">
                            <span class="uk--box-label">Fragrance</span>
                            <span class="uk--box-text">@{{ order.detail.fragrance }}</span>
                        </div>
                        <div v-if="order.detail.total_items" class="uk-width-1-5">
                            <span class="uk--box-label">Total Items</span>
                            <span class="uk--box-text">@{{ order.detail.total_items }}</span>
                        </div>
                        <div v-if="order.detail.delivery_date" class="uk-width-1-5">
                            <span class="uk--box-label">Delivery Date</span>
                            <span class="uk--box-text">@{{ order.detail.delivery_date }}</span>
                        </div>
                    </div>
                    <div class="uk-grid-collapse uk-margin-top" uk-grid>
                        <div class="uk-width-expand">
                            <span class="uk--box-label">Note</span>
                            <span class="uk--box-text">@{{ order.note }}</span>
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
                                <td class="uk-text-right">@{{ item.human_prices.price }}</td>
                                <td class="uk-text-right">@{{ item.pivot.quantity }}</td>
                                <td class="uk-text-right">@{{ item.human_prices.total }}</td>
                            </tr>
                            <tr v-if="order.extra_price_cso > 0">
                                <td>Additional CSO</td>
                                <td class="uk-text-right">@{{ order.human_prices.amount }}</t>
                                <td class="uk-text-right">@{{ order.human_prices.additional_cso }}</td>
                                <td class="uk-text-right">@{{ order.human_prices.extra_price_cso }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td class="uk-text-bold" colspan="3">SUB TOTAL</td>
                                <td class="uk-text-right">@{{ order.human_prices.sub_total }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-bold" colspan="3">DISCOUNT</td>
                                <td class="uk-text-right">@{{ order.human_prices.discount }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-bold" colspan="3">TOTAL</td>
                                <td class="uk-text-bold uk-text-right">@{{ order.human_prices.final_total }}</td>
                            </tr>
                        </tfoot>
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
        <div class="uk-card-footer">
            <div class="uk--box-label">Created By</div>
            <div class="uk--box-text">@{{ order.invoice.created_by }}</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#content',

        data: {
            order: {},
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
            getOrder() {
                axios.get(`${Laravel.url}/admin/json/orders/{{ $id }}`)
                    .then(({ data }) => {
                        if (data.error) {
                            this.$notify({
                                title: 'Error',
                                message: data.message,
                                type: 'error'
                            })

                            return
                        }

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
                    _method: 'PATCH',
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
            }
        },

        created() {
            this.getOrder()
        }
    })
</script>
@endsection