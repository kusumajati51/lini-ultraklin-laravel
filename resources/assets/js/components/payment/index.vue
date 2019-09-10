<template>
  <v-app>
    <v-content>
      <v-container fill-height>
        <v-layout column>
          <v-flex xs1>
            <v-card>
              <v-card-title primary-title>
                <v-layout row>
                  <v-flex> <h3 class="headline mb-0">Amount</h3> </v-flex>
                  <v-layout row justify-end align-center>
                    <div class="title">
                      IDR {{ invData.invId.details.total }}
                    </div>
                  </v-layout>
                </v-layout>
              </v-card-title>
            </v-card>
          </v-flex>
          <v-flex xs1 class="mt-4">
            <div class="subheading">Select your payment method</div>
            <v-expansion-panel v-model="paymentMethod" :popout="true">
              <v-expansion-panel-content hide-actions>
                <v-layout slot="header" row align-center>
                  <v-flex>Cash</v-flex>
                  <v-flex xs1 class="text-xs-right">
                    <v-icon>fas fa-caret-right</v-icon>
                  </v-flex>
                </v-layout>
              </v-expansion-panel-content>
              <v-expansion-panel-content hide-actions :disabled="channelDeact">
                <v-layout slot="header" row align-center>
                  <v-flex
                    >Transfer
                    <div v-show="channelDeact">Minimum amount IDR 10.000</div>
                  </v-flex>
                  <v-flex xs1 class="text-xs-right">
                    <v-icon>fas fa-caret-right</v-icon>
                  </v-flex>
                </v-layout>
                <v-layout justify-center>
                  <v-progress-circular
                    v-if="loading.channelData"
                    indeterminate
                  ></v-progress-circular>
                </v-layout>
                <v-radio-group v-model="channelGrp">
                  <v-radio
                    class="mb-4 ml-3"
                    v-for="channel in channels"
                    :key="channel.pg_code"
                    :label="channel.pg_name"
                    :value="channel"
                  ></v-radio>
                </v-radio-group>
              </v-expansion-panel-content>
              <v-expansion-panel-content v-show="false" hide-actions>
                <v-layout slot="header" row align-center>
                  <v-flex>Kartu Kredit</v-flex>ß
                  <v-flex xs1 class="text-xs-right">
                    <v-icon>fas fa-caret-right</v-icon>
                  </v-flex>
                </v-layout>
                <v-layout column mx-3>
                  <v-text-field label="Card name"></v-text-field>
                  <v-text-field
                    mask="credit-card"
                    label="Card Number"
                  ></v-text-field>
                  <v-layout row>
                    <v-flex mr-1>
                      <v-text-field
                        mask="##/##"
                        label="Expire Date"
                        mx-1
                      ></v-text-field>
                    </v-flex>
                    <v-flex ml-1>
                      <v-text-field mask="###" label="CVV" mx-1></v-text-field>
                    </v-flex>
                  </v-layout>
                  <v-checkbox label="Save card"></v-checkbox>
                </v-layout>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-flex>
          <v-layout class="align-end justify-end mt-3">
            <v-btn class="title" @click.prevent="sendOrder" block color="info"
              >Pay</v-btn
            >
          </v-layout>
        </v-layout>
      </v-container>
      <v-dialog v-model="fullLoadingDialog" persistent width="300">
        <v-card color="primary" dark>
          <v-card-text
            >Please stand by
            <v-progress-linear
              indeterminate
              color="white"
              class="mb-0"
            ></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="alert.active" :top="true" :timeout="3000">
        {{ alert.message }}
        <v-btn flat color="primary" @click.native="alert.active = false"
          >Close</v-btn
        >
      </v-snackbar>
    </v-content>
  </v-app>
</template>

<script>
/*eslint-disable*/
import sign from '../sign.js'
const crypt = require('crypto-js')

export default {
	name: 'PaymentChannel',
	props: ['invData'],
	data: function() {
		return {
			channels: [],
			channelGrp: null,
			resInvData: {},
			fullLoadingDialog: false,
			paymentMethod: null,
			total: this.invData.invId.details.total,
			alert: {
				active: false,
				message: ''
			},
			loading: {
				channelData: true
			},
			check: {
				cash: false,
				debit: false,
				credit: false
			},
			channelDeact: true,
			profile: {}
		}
	},
	methods: {
		async initComp() {
			await this.getProfile()
			await this.getPaymentChannel()
			await this.checkTotal()
		},
		getProfile() {
			return new Promise((resolve, reject) => {
				var config = {
					headers: {
						'Content-Type': 'application/json',
						Authorization: this.invData.token
					}
				}
				axios.get('/user/profile', config).then(res => {
					this.profile = res.data
					console.log(1)
					resolve()
				})
			})
		},
		getPaymentChannel() {
			return new Promise((resolve, reject) => {
				axios.get('/v1/payment/channels').then(result => {
						this.channels = result.data
						this.loading.channelData = false
						console.log(2)
						resolve()
					}).catch(err => {
						console.log(err)
					})
			})
		},
		getInvoices(code) {
			return new Promise(resolve => {
				var config = {
					headers: {
						'Content-Type': 'application/json',
						Authorization: this.invData.token
					}
				}
				axios
					.get('/invoices/' + code, config)
					.then(res => {
						let to
						switch (this.paymentMethod) {
							case 0:
								resolve((window.location = axios.defaults.baseURL + '/finish'))
								break
							case 1:
								to = this.sendPaymentDebit(res.data)
								resolve(to)
								break
							case 2:
								to = this.sendPaymentCredit(res.data)
								resolve(to)
								break
							default:
								this.showAlert('Error access')
								break
						}
						// console.log(res.data)
					})
					.catch(err => {
						console.log(err)
					})
			})
		},
		sendPaymentDebit(param) {
			var config = {
				headers: {
					'Content-Type': 'application/json',
					Authorization: this.invData.token
				}
			}
			let add_detail = {
				url: '300011/10',
				method: 'debit',
				content_type: 'json',
				bank: this.channelGrp.pg_name
			}
			let itemArray = []
			param.items.forEach(v => {
				let el = {}
				el.product = v.package
				el.qty = v.quantity
				el.amount = v.price + '00'
				el.payment_plan = '01'
				el.merchant_id = '32194'
				el.tenor = '00'
				itemArray.push(el)
			})
			let data = {
				request: 'Transmisi Info Detil Pembelian',
				merchant_id: sign.user.merch_id,
				merchant: sign.user.merch_name,
				bill_no: param.code,
				bill_reff: param.created_by,
				bill_date: param.created_at,
				bill_expired: this.$moment()
					.add(2, 'h')
					.format('YYYY-MM-DD HH:mm:ss'),
				bill_desc: 'Payment ' + param.code,
				bill_currency: 'IDR',
				bill_gross: '0',
				bill_miscfee: '0',
				bill_total: this.invData.invId.details.total + '00',
				cust_no: '12',
				cust_name: this.profile.name,
				payment_channel: this.channelGrp.pg_code,
				pay_type: '1',
				bank_userid: '',
				msisdn: '31254623413414',
				email: this.profile.email,
				terminal: '10',
				billing_name: '0',
				billing_lastname: '0',
				billing_address: param.orders[0].location,
				billing_address_city: '',
				billing_address_region: '',
				billing_address_state: '',
				billing_address_poscode: '',
				billing_msisdn: '',
				billing_address_country_code: 'ID',
				receiver_name_for_shipping: this.profile.name,
				shipping_lastname: '',
				shipping_address: param.orders[0].location,
				shipping_address_city: '',
				shipping_address_region: '',
				shipping_address_state: '',
				shipping_address_poscode: '',
				shipping_msisdn: '',
				shipping_address_country_code: 'ID',
				item: itemArray,
				reserve1: '',
				reserve2: '',
				signature: crypt
					.SHA1(crypt.MD5(sign.id + sign.pass + param.code).toString())
					.toString()
			}
			axios
				.post('/send-payment/' + param.id, { data, add_detail }, config)
				.then(res => {
					// console.log(res.data)
					this.fullLoadingDialog = false
					window.location = res.data.redirect_url
				})
				.catch(err => {
					console.log(err)
					this.fullLoadingDialog = false
				})
		},
		sendPaymentCredit(param) {
			var config = {
				headers: {
					// "Content-Type": "application/json",
					Authorization: this.invData.token
				}
			}
			let add_detail = {
				url: 'https://fpgdev.faspay.co.id/payment',
				method: 'credit',
				content_type: 'form_params',
				sign: sign.credit_id,
				data: {
					total: param.total,
					code: param.code
				}
			}
			console.log(
				crypt
					.SHA1(
						'##' +
							sign.credit_id.merch_id.toUpperCase() +
							'##' +
							sign.credit_id.pass.toUpperCase() +
							'##' +
							param.code +
							'##' +
							param.total +
							'.00##0##'
					)
					.toString()
			)
			let data = {
				TRANSACTIONTYPE: '1',
				//"SHOPPER_IP"          : '192.168.130.130',
				RESPONSE_TYPE: '2',
				LANG: '',
				MERCHANTID: sign.credit_id.merch_id,
				PAYMENT_METHOD: '1',
				TXN_PASSWORD: sign.credit_id.pass,
				MERCHANT_TRANID: param.code,
				CURRENCYCODE: 'IDR',
				AMOUNT: this.invData.invId.details.total + '.00',
				CUSTNAME: this.profile.name,
				CUSTEMAIL: this.profile.email,
				DESCRIPTION: 'CC ' + param.code,
				RETURN_URL: 'http://localhost/creditcard/merchant_return_page.php',
				SIGNATURE: crypt
					.SHA1(
						'##' +
							sign.credit_id.merch_id.toUpperCase() +
							'##' +
							sign.credit_id.pass.toUpperCase() +
							'##' +
							param.code +
							'##' +
							param.total +
							'.00##0##'
					)
					.toString(),
				BILLING_ADDRESS: param.orders[0].location,
				BILLING_ADDRESS_CITY: 'Jakarta',
				BILLING_ADDRESS_REGION: 'Jakarta',
				BILLING_ADDRESS_STATE: 'Jakarta',
				BILLING_ADDRESS_POSCODE: '12220',
				BILLING_ADDRESS_COUNTRY_CODE: 'ID',
				RECEIVER_NAME_FOR_SHIPPING: param.created_by,
				SHIPPING_ADDRESS: param.orders[0].location,
				SHIPPING_ADDRESS_CITY: 'Jakarta',
				SHIPPING_ADDRESS_REGION: 'Jakarta',
				SHIPPING_ADDRESS_STATE: 'Jakarta',
				SHIPPING_ADDRESS_POSCODE: '12220',
				SHIPPING_ADDRESS_COUNTRY_CODE: 'ID',
				SHIPPINGCOST: '0.00',
				PHONE_NO: '0897867688989',
				MREF1: '',
				MREF2: '',
				MREF3: '',
				MREF4: '',
				MREF5: '',
				MREF6: '',
				MREF7: '',
				MREF8: '',
				MREF9: '',
				MREF10: '',
				MPARAM1: '',
				MPARAM2: '',
				CUSTOMER_REF: '',
				PYMT_IND: '',
				PYMT_CRITERIA: '',
				PYMT_TOKEN: '',
				//paymentoption               : '0',
				FRISK1: '',
				FRISK2: '',
				DOMICILE_ADDRESS: param.orders[0].location,
				DOMICILE_ADDRESS_CITY: '',
				DOMICILE_ADDRESS_REGION: '',
				DOMICILE_ADDRESS_STATE: '',
				DOMICILE_ADDRESS_POSCODE: '',
				DOMICILE_ADDRESS_COUNTRY_CODE: '',
				DOMICILE_PHONE_NO: '',
				handshake_url: '',
				handshake_param: '',
				style_merchant_name: 'black',
				style_order_summary: 'black',
				style_order_no: 'black',
				style_order_desc: 'black',
				style_amount: 'black',
				style_background_left: '#fff',
				style_button_cancel: 'grey',
				style_font_cancel: 'white'
				//harus url yg lgsg ke gambar
				//style_image_url"           : 'http://www.pikiran-rakyat.com/sites/files/public/styles/medium/public/image/2017/06/Logo%20HUT%20RI%20ke-72%20yang%20paling%20bener.jpg?itok=RsQpqpqD',
			}
			axios
				.post('/send-payment/' + param.id, { data, add_detail }, config)
				.then(res => {
					// console.log(res.data)
					this.fullLoadingDialog = false
					// window.location = res.data.redirect_url
					document.write(res.data)
				})
				.catch(err => {
					console.log(err)
					this.showAlert(error)
					this.fullLoadingDialog = false
				})
		},
		sendOrder() {
			if (this.paymentMethod != null) {
				if (this.paymentMethod == 1 && this.channelGrp == null) {
					return this.showAlert('Please select channel')
				}
				this.fullLoadingDialog = true
				return new Promise(resolve => {
					var config = {
						headers: {
							// 'Content-Type': 'application/json',
							Accept: 'application/json',
							Authorization: this.invData.token
						}
					}
					let body = this.invData.invId
					switch (this.paymentMethod) {
						case 0:
							body['payment'] = 'Cash'
							break
						case 1:
							body['payment'] = 'Transfer'
							break
						case 2:
							body['payment'] = 'Credit Card'
							break
						default:
							this.showAlert('Error access')
							break
					}
					// console.log(JSON.parse(body))
					axios
						.post('/v1/orders', body, config)
						.then(res => {
							// console.log(res.data)
							if (res.data.error) {
								this.fullLoadingDialog = false
								return this.showAlert(res.data.message)
							}
							let to = this.getInvoices(res.data.data.code)
							resolve(to)
						})
						.catch(err => {
							console.log('error from sendOrder: ' + err)
							this.fullLoadingDialog = false
						})
				})
			} else {
				this.showAlert('Please select payment method.')
			}
		},
		showAlert(msg) {
			this.alert = {
				active: true,
				message: msg
			}
		},
		checkTotal() {
			return new Promise((resolve, reject) => {
				if (this.total < 10000) {
					this.channelDeact = true
				} else {
					this.channelDeact = false
				}
				console.log(3)
				resolve()
			})
		}
	},
	mounted() {
		this.initComp()
		console.log(process.env)
	}
}
</script>

<style scoped>
.v-input {
	margin-top: 0px;
}
</style>
