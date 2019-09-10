<!DOCTYPE html>
<html>
    <head>
        <meta name="url" content="{{ url('/') }}">
        <meta name="token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/pace-theme-cube.css') }}">
        <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/theme-chalk/index.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/ultraklin.css') }}?{{ time() }}">
    </head>
    <body>
        <div id="app" class="uk-container uk-margin-top uk-margin-bottom">
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-expand">
                    <div class="uk-card uk-card-default uk-card-small">
                        <div class="uk-card-header uk-padding-remove">
                            <div class="uk--tool-header uk-grid-collapse" uk-grid>
                                <div class="uk-width-auto">
                                    <a class="uk--tool-header-back" href="{{ url('/admin/orders') }}">
                                        <i class="fas fa-angle-left fa-lg"></i>
                                    </a>
                                </div>
                                <div class="uk-width-expand">
                                    <h3 class="uk--tool-header-title">NEW ORDER</h3>
                                </div>
                            </div>
                        </div>
                        <div class="uk-card-body">
                            <div class="uk-margin">
                                <label class="uk-form-label">Region</label>
                                <div>
                                    <el-select v-model="input.region" size="mini" placeholder="Choose region" @change="onRegionChange">
                                        <el-option v-for="item in options.region"
                                            :key="item.value"
                                            :value="item.value"
                                            :label="item.label"></el-option>
                                    </el-select>
                                </div>
                            </div>
                            <div class="uk-margin">
                                <label class="uk-form-label">Customer</label>
                                <div>
                                    <el-autocomplete
                                        class="uk-width-medium"
                                        v-model="input.customerString"
                                        :fetch-suggestions="customerSearch"
                                        value-key="name"
                                        placeholder="Select customer"
                                        size="mini"
                                        @select="handleCustomerSelect">
                                        <el-button slot="append" @click="dialogFormVisible = true">or new</el-button>
                                    </el-autocomplete>
                                </div>
                            </div>
                            <div class="uk-grid-small uk-child-width-1-1" uk-grid>
                                <div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label">Select Package</label>
                                        <div>
                                            <el-select v-model="input.package" class="uk-width-medium" size="mini" @change="changePackage">
                                                <el-option
                                                    v-for="package in packages"
                                                    :key="package.name"
                                                    :value="package.name"
                                                    :label="package.display_name">
                                                    @{{ package.display_name }}
                                                </el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label">Date / Time</label>
                                        <div>
                                            <el-date-picker
                                                v-model="input.date"
                                                format="dd-MM-yyyy"
                                                value-format="yyyy-MM-dd"
                                                :picker-options="datePickerOptions"
                                                size="mini"></el-date-picker>
                                            <el-time-select
                                                v-model="input.time"
                                                :picker-options="timePickerOptions"
                                                size="mini"></el-time-picker>
                                        </div>
                                    </div>
                                    <div class="uk-margin">
                                        <label class="uk-form-label">Location</label>
                                        <div>
                                            <el-input v-model="input.location" size="mini"></el-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div v-if="service == 'cleaning'" class="uk-margin">
                                <div class="uk-grid-small uk-child-width-1-4" uk-grid>
                                    <div>
                                        <label class="uk-form-label">Building Type</label>
                                        <el-select v-model="input.detail.building_type" size="mini">
                                            <el-option v-for="item in options.building"
                                                :key="item.value"
                                                :value="item.value"
                                                :label="item.label"></el-option>
                                        </el-select>
                                    </div>
                                    <div>
                                        <label class="uk-form-label">CSO Gender</label>
                                        <el-select v-model="input.detail.cso_gender" size="mini">
                                            <el-option v-for="item in options.gender"
                                                :key="item.value"
                                                :value="item.value"
                                                :label="item.label"></el-option>
                                        </el-select>
                                    </div>
                                    <div>
                                        <label class="uk-form-label">Pets</label>
                                        <el-select v-model="input.detail.pets" size="mini">
                                            <el-option v-for="item in options.pets"
                                                :key="item.value"
                                                :value="item.value"
                                                :label="item.label"></el-option>
                                        </el-select>
                                    </div>
                                </div>
                            </div>
                            <div v-if="service == 'laundry-pieces' || service == 'laundry-kilos'" class="uk-margin">
                                <div class="uk-grid-small uk-child-width-1-4" uk-grid>
                                    <div>
                                        <label class="uk-form-label">Fragrance</label>
                                        <el-select v-model="input.detail.fragrance" size="mini">
                                            <el-option v-for="item in options.fragrance"
                                                :key="item.value"
                                                :value="item.value"
                                                :label="item.label"></el-option>
                                        </el-select>
                                    </div>
                                    <div v-if="service == 'laundry-kilos'">
                                        <label class="uk-form-label">Total Items</label>
                                        <div>
                                            <el-input-number v-model="input.detail.total_items" :min="0" size="mini"></el-input-number>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <el-autocomplete
                                    v-model="input.itemQuery"
                                    :fetch-suggestions="itemSearch"
                                    value-key="name"
                                    size="small"
                                    class="uk-width-medium"
                                    placeholder="Select item"
                                    name="item_query"
                                    @select="handleItemSelect"></el-autocomplete>
                            </div>
                            <hr>
                            <div class="uk-oveflow-auto">
                                <table class="uk-table uk-table-small uk-text-small uk-table-divider">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="uk-text-right" width="100px">Price</th>
                                            <th class="uk-text-center" width="100px">Quantity</th>
                                            <th class="uk-text-right" width="100px">Sub Total</th>
                                            <th class="uk-text-center" width="50px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in input.items">
                                            <td>@{{ item.name }}</td>
                                            <td class="uk-text-right">@{{ item.price }}</td>
                                            <td class="uk-text-right">
                                                <el-input-number v-model="input.items[index].quantity" :min="1" size="mini"></el-input-number>
                                            </td>
                                            <td class="uk-text-right">@{{ item.price * item.quantity }}</td>
                                            <td class="uk-text-center">
                                                <a class="uk-text-danger" href="#" @click.prevent="removeItem(index)"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="uk-margin-top uk-text-center">
                                <el-button v-if="input.items.length > 0" class="uk-text-uppercase" type="primary" size="small" round @click="addToCart">Add to cart</el-button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium">
                    <div class="uk-card uk-card-default uk-card-small">
                        <div class="uk-card-header uk-padding-remove">
                            <div class="uk--tool-header uk-grid-collapse" uk-grid>
                                <div class="uk-width-auto">
                                    <div class="uk--tool-header-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                </div>
                                <div class="uk-width-expand">
                                    <h3 class="uk--tool-header-title">CART</h3>
                                </div>
                            </div>
                        </div>
                        <div class="uk-card-body">
                            <ul class="uk--simple-cart-item uk-list uk-list-divider">
                                <li v-for="(order, index) in cart">
                                    <div class="uk-grid-small" uk-grid>
                                        <div class="uk-width-expand uk-text-truncate">
                                            <h5>@{{ order.package | packageForHuman(packages) }}</h5>
                                        </div>
                                        <div class="uk-width-1-5">
                                            <span>@{{ order.items.length }} Item(s)</span>
                                        </div>
                                        <div class="uk-width-auto">
                                            <a class="uk-text-danger" href="#" @click.prevent="removeOrder(index)"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="uk-card-footer uk-text-center">
                            <el-button class="uk-width-1-1 uk-text-uppercase" type="primary" size="small" round :disabled="cart.length == 0" @click="openPreview">Next</el-button>
                        </div>
                    </div>
                </div>
            </div>
            <el-dialog title="Add Customer" :visible.sync="dialogFormVisible">
                <form>
                    <div class="uk-margin">
                        <el-input v-model="dialogForm.name" size="small" placeholder="Name"></el-input>
                        <p v-if="dialogFormErrors.name" class="uk-margin-small uk-text-danger">@{{ dialogFormErrors.name[0] }}</p>
                    </div>
                    <div class="uk-margin">
                        <el-input v-model="dialogForm.phone" size="small" placeholder="Phone"></el-input>
                        <p v-if="dialogFormErrors.phone" class="uk-margin-small uk-text-danger">@{{ dialogFormErrors.phone[0] }}</p>
                    </div>
                    <div class="uk-margin">
                        <el-input v-model="dialogForm.email" size="small" placeholder="Email"></el-input>
                        <p v-if="dialogFormErrors.email" class="uk-margin-small uk-text-danger">@{{ dialogFormErrors.email[0] }}</p>
                    </div>
                </form>
                <span slot="footer" class="dialog-footer">
                    <el-button type="danger" size="small" @click="dialogFormVisible = false">Cancel</el-button>
                    <el-button type="primary" size="small" @click="saveCustomer">Confirm</el-button>
                </span>
            </el-dialog>
            @include('admin.offline_order.preview')
        </div>
        <script src="{{ asset('js/pace.min.js') }}"></script>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/uikit.min.js') }}"></script>
        <script src="{{ asset('js/vue.min.js') }}"></script>
        <script src="{{ asset('js/element-ui.min.js') }}"></script>
        <script src="{{ asset('js/element-ui-en.js') }}"></script>
        <script src="{{ asset('js/axios.min.js') }}"></script>
        <script src="{{ asset('js/lodash.min.js') }}"></script>
        <script>
            window.Laravel = {
                url: document.querySelector('meta[name=url]').getAttribute('content'),
                token: document.querySelector('meta[name=token').getAttribute('content')
            }
            
            Vue.config.devtools = true
            
            ELEMENT.locale(ELEMENT.lang.en)

            new Vue({
                el: '#app',

                data: {
                    packages: [],
                    input: {
                        region: '',
                        package: '',
                        date: `${new Date().getFullYear()}-${('0' + (new Date().getMonth() + 1)).slice(-2)}-${('0' + new Date().getDate()).slice(-2)}`,
                        time: '12:00',
                        location: '',
                        customer: '',
                        customerString: '',
                        detail: {},
                        itemQuery: '',
                        items: []
                    },
                    inputErrors: [],
                    options: {
                        region: JSON.parse('{!! json_encode($regions) !!}'),
                        building: [
                            {
                                label: 'Home',
                                value: 'Home'
                            },
                            {
                                label: 'Apartemen',
                                value: 'Apartemen'
                            },
                            {
                                label: 'Office',
                                value: 'Office'
                            }
                        ],
                        gender: [
                            {
                                label: 'Man',
                                value: 'Man'
                            },
                            {
                                label: 'Woman',
                                value: 'Woman'
                            },
                            {
                                label: 'Any',
                                value: 'Any'
                            }
                        ],
                        pets: [
                            {
                                label: 'Yes',
                                value: 'Yes'
                            },
                            {
                                label: 'No',
                                value: 'No'
                            }
                        ],
                        fragrance: [
                            {
                                label: 'Yes',
                                value: 'Yes'
                            },
                            {
                                label: 'No',
                                value: 'No'
                            }
                        ]
                    },
                    cart: [],
                    service: '',
                    datePickerOptions: {
                        disabledDate(time) {
                            return time.getTime() < new Date().setDate(new Date().getDate() - 1)
                        }
                    },
                    timePickerOptions: {
                        tart: '09:00',
                        end: '19:00'
                    },
                    dialogFormVisible: false,
                    dialogForm: {
                        name: '',
                        email: '',
                        phone: '',
                        region: ''
                    },
                    dialogFormErrors: {}
                },

                watch: {
                    'input.package': {
                        handler(val) {
                            let package = _.find(this.packages, { name: val })

                            if (package == undefined) {
                                this.service = ''

                                return
                            }

                            this.service = package.service_name
                        }
                    }
                },

                filters: {
                    humanKey(val) {
                        return val.replace(/(_)/g, ' ')
                    },
                    packageForHuman(val, packages) {
                        let package = _.find(packages, { name: val })

                        return package.display_name
                    },
                    regionForHuman(val, regions) {
                        let region = _.find(regions, { value: val })

                        if (region == undefined) return

                        return region.label
                    },
                    totalPrice(val) {
                        return _.sumBy(val, (item) => {
                            return item.price * item.quantity
                        })
                    }
                },

                methods: {
                    onRegionChange() {
                        this.getPackages()
                        
                        this.input.items = []
                    },
                    customerSearch(queryString, callback) {
                        let items = []

                        axios.get(`${Laravel.url}/admin/json/customers`, {
                            params: {
                                region: this.input.region,
                                search: queryString
                            }
                        }).then(({data}) => {
                            items = data

                            callback(items)
                        }).catch(error => {

                        })
                    },
                    handleCustomerSelect(item) {
                        this.input.customer = item.id
                    },
                    saveCustomer() {
                        this.dialogForm.region = this.input.region
                        this.dialogFormErrors = {}

                        axios.post(`${Laravel.url}/admin/json/customers`, this.dialogForm)
                            .then(({data}) => {
                                if (data.error_validation) {
                                    this.dialogFormErrors = data.data

                                    return
                                }

                                this.input.customerString = data.data.name
                                this.input.customer = data.data.id

                                this.dialogFormVisible = false

                                this.dialogForm = {
                                    name: '',
                                    phone: '',
                                    email: '',
                                    region: ''
                                }
                            })
                            .catch(error => {
                                this.$notify({
                                    title: error.response.status,
                                    message: error.response.statusText,
                                    type: 'error'
                                })
                            })
                    },
                    getPackages() {
                        axios.get(`${Laravel.url}/api/v1/packages/list`, {
                            params: {
                                region: this.input.region
                            }
                        }).then(({data}) => {
                            this.packages = data

                            if (data.length == 0) {
                                this.input.package = ''

                                return
                            }

                            this.input.package = data[0].name
                        }).catch(error => {

                        })
                    },
                    itemSearch(queryString, callback) {
                        let items = []

                        axios.get(`${Laravel.url}/api/v1/packages/${this.input.package}/items/list`, {
                            params: {
                                search: this.input.itemQuery
                            }
                        }).then(({data}) => {
                            items = data

                            callback(items)
                        }).catch(error => {

                        })
                    },
                    handleItemSelect(item) {
                        $('input[name=item_query]').focus()
                        
                        let self = this

                        this.input.itemQuery = ''

                        let index = _.findIndex(this.input.items, { id: item.id })

                        if (index < 0) {
                            this.input.items.push({
                                id: item.id,
                                name: item.name,
                                price: item.price,
                                quantity: 1
                            })

                            return
                        }

                        this.input.items[index].quantity += 1
                    },
                    removeItem(index) {
                        this.input.items.splice(index, 1)
                    },
                    changePackage() {
                        this.input.itemQuery = ''
                        this.input.items = []
                    },
                    addToCart() {
                        this.cart.push({
                            region: this.input.region,
                            package: this.input.package,
                            date: `${this.input.date} ${this.input.time}`,
                            location: this.input.location,
                            detail: this.input.detail,
                            items: this.input.items
                        })

                        this.input.package = this.packages[0].name,
                        this.input.date = `${new Date().getFullYear()}-${('0' + (new Date().getMonth() + 1)).slice(-2)}-${('0' + new Date().getDate()).slice(-2)}`,
                        this.input.time = '12:00',
                        this.input.location = '',
                        this.input.detail = {},
                        this.input.itemQuery = '',
                        this.input.items = []
                    },
                    removeOrder(index) {
                        this.cart.splice(index, 1)
                    },
                    openPreview() {
                        UIkit.modal('#modal-preview').show()
                    },
                    submitOrder() {
                        let self = this

                        let form = {
                            csrf_token: Laravel.token,
                            customer: this.input.customer,
                            payment: 'Cash',
                            orders: this.cart
                        }

                        axios.post(`${Laravel.url}/admin/json/offline-orders`, form).then(({ data }) => {
                            if (data.error_validation) {
                                this.inputErrors = data.data

                                this.$notify({
                                    title: 'Error',
                                    message: data.message,
                                    type: 'error'
                                })

                                return
                            }
                            else if (data.error) {
                                this.$notify({
                                    title: 'Error',
                                    message: data.message,
                                    type: 'error'
                                })

                                return
                            }

                            this.$notify({
                                title: 'Success',
                                message: data.message,
                                type: 'success'
                            })

                            self.cart = []

                            UIkit.modal('#modal-preview').hide()
                        }).catch(error => {
                            this.$notify({
                                title: error.response.status,
                                message: error.response.statusText,
                                type: 'error'
                            })
                        })
                    }
                },

                mounted() {
                    this.input.region = this.options.region[0].value

                    this.getPackages()
                }
            })
        </script>
    </body>
</html>