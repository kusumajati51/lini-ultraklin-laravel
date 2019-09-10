@extends('admin._master') 
@section('content')
<div id="invoice-detail" class="uk-card uk-card-default uk-card-small">
	<div class="uk-card-header uk-padding-remove">
		<div class="uk--tool-header uk-grid-collapse" uk-grid>
			<div class="uk-width-auto">
				<a class="uk--tool-header-back" href="{{ url()->previous() }}">
					<i class="fas fa-angle-left fa-lg"></i>
				</a>
			</div>
			<div class="uk-width-expand">
				<h3 class="uk--tool-header-title">INVOICE DETAIL</h3>
			</div>
			<div class="uk-width-auto">
				<div class="uk--tool-header-icon">
					<a class="uk-link-reset" href="#" @click="print">
						<i class="fas fa-print"></i>
					</a>
				</div>
			</div>
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
					<div v-if="invoice.user || invoice.customer" class="uk-width-1-5">
						<span class="uk--box-label">Customer</span>
						<span class="uk--box-text">@{{ (invoice.user == null) ? invoice.customer.name : invoice.user.name }}</span>
					</div>
					<div class="uk-width-1-5">
						<span class="uk--box-label">Status</span>
						<span class="uk--box-text"><el-tag :type="tagStatus[invoice.status]" size="mini">@{{ invoice.status }}</el-tag></span>
					</div>
					<div class="uk-width-1-5">
						<span class="uk--box-label">Promo</span>
						<span class="uk--box-text">@{{ (invoice.promotion) ? invoice.promotion.code : 'None' }}</span>
					</div>
				</div>
			</div>
			<!-- invoice-box-header -->
			<div class="uk--box-body-with-padding">
				<div class="uk-grid-collapse" uk-grid>
					<div class="uk-width-1-4">
						<span class="uk--box-label">Payment</span>
						<span class="uk--box-text">@{{ invoice.payment }}</span>
					</div>
					<div class="uk-width-1-4">
						<span class="uk--box-label">Sub Total</span>
						<span class="uk--box-text">@{{ invoice.human_prices ? invoice.human_prices.total : 0 }}</span>
					</div>
					<div class="uk-width-1-4">
						<span class="uk--box-label">Discount</span>
						<span class="uk--box-text">@{{ invoice.human_prices ? invoice.human_prices.discount : 0 }}</span>
					</div>
					<div class="uk-width-1-4">
						<span class="uk--box-label">Total</span>
						<span class="uk--box-text">@{{ invoice.human_prices ? invoice.human_prices.final_total : 0 }}</span>
					</div>
				</div>
			</div>
			<!-- invoice-box-body -->
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
			</div>
			<!-- invoice-box-footer -->
		</div>
		<!-- invoice-box -->

		{{--
		<div v-if="invoice.ratings" class="uk--box">
			<div class="uk--box-header">
				<div class="uk-grid-collapse" uk-grid>
					<div class="uk-width-4-5">
						<span class="uk--box-label">Comment</span>
						<span class="uk--box-text">@{{ invoice.ratings.comments }}</span>
					</div>
					<div class="uk-width-1-5">
						<span class="uk--box-label">Rating</span>
						<span v-if="invoice.ratings.votes === 1" style="font-size: 32px; color: Dodgerblue;"><i class="far fa-thumbs-down"></i></span>
						<span v-if="invoice.ratings.votes === 2" style="font-size: 32px; color: Dodgerblue;"><i class="far fa-meh"></i></span>
						<span v-if="invoice.ratings.votes === 3" style="font-size: 32px; color: Dodgerblue;"><i class="far fa-thumbs-up"></i></i></span>
					</div>
				</div>
			</div>
		</div> --}}

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
						<span class="uk--box-text"><el-tag :type="tagStatus[order.status]" size="mini">@{{ order.status }}</el-tag></span>
					</div>
				</div>
				<div class="uk-grid-collapse uk-margin-top" uk-grid>
					<div class="uk-width-3-5">
						<span class="uk--box-label">Location</span>
						<span class="uk--box-text">@{{ order.location }}</span>
					</div>
					<div class="uk-width-1-5">
						<span class="uk--box-label">Customer</span>
						<span class="uk--box-text">@{{ (invoice.user == null) ? invoice.customer.name : invoice.user.name }}</span>
					</div>
					<div class="uk-width-1-5">
						<span class="uk--box-label">Phone</span>
						<span class="uk--box-text">@{{ (invoice.user == null) ? invoice.customer.phone : invoice.user.phone }}</span>
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
					<div class="uk-width-expand">
						<span class="uk--box-label">Comment</span>
						<span v-if="order.ratings" class="uk--box-text">@{{ order.ratings.comments }}</span>
					</div>
					<div class="uk-width-1-6">
						<span class="uk--box-label">Rate</span>
						<span v-if="order.ratings && order.ratings.votes === 1" style="font-size: 32px; color: Dodgerblue;"><i class="far fa-thumbs-down"></i></span>
						<span v-if="order.ratings && order.ratings.votes === 2" style="font-size: 32px; color: Dodgerblue;"><i class="far fa-meh"></i></span>
						<span v-if="order.ratings && order.ratings.votes === 3" style="font-size: 32px; color: Dodgerblue;"><i class="far fa-thumbs-up"></i></i></span>
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
							<td class="uk-text-right">@{{ item.human_prices.sub_total }}</td>
						</tr>
						<tr v-if="order.prices.extra_price_cso > 0">
							<td>Additional CSO</td>
							<td class="uk-text-right">@{{ order.human_prices.amount }}</td>
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
					{{--
					<div class="uk-with-auto">
						<el-autocomplete class="inline-input" placeholder="Input CSO" :trigger-on-focus="false" size="small" :disabled="!order.edit"></el-autocomplete>
					</div> --}}
					<div class="uk-width-auto@m">
						<el-button v-if="order.edit" type="danger" size="small" @click="cancelEditOrder(index)">Cancel</el-button>
						<el-button v-if="order.edit" type="success" size="small" :loading="order.editLoading" @click="updateOrder(index)">Done</el-button>
						<el-button v-else type="primary" size="small" @click="editOrder(index)">Edit</el-button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="uk-card-footer">
		<div class="uk--box-label">Created By</div>
		<div class="uk--box-text">@{{ invoice.created_by }}</div>
	</div>
</div>
@endsection
 
@section('scripts')
<script>
	new Vue({
		el: '#invoice-detail',

		data: {
			invoice: {},
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
			getInvoice() {
				axios.get(Laravel.url + '/admin/json/invoices/{{ $code }}')
					.then(({ data }) => {
						if (data.error) {
							this.$notify({
								title: 'Error',
								message: data.message,
								type: 'error'
							})

							return
						}
						
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
			print() {
				let printWindow = window.open(`${Laravel.url}/admin/invoices/{{ $code }}/print`, 'UltraKlin', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=400')

				printWindow.focus()
				printWindow.print()
			}
		},

		mounted() {
			this.getInvoice()
		}
	})

</script>
@endsection