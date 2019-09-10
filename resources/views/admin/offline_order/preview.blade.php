<div id="modal-preview" class="uk-modal-full" uk-modal>
    <div class="uk-modal-dialog">
        <a class="uk-modal-close-default uk-text-danger" href="#"><i class="fas fa-times-circle fa-lg"></i></a>
        <div uk-height-viewport>
            <div class="uk-padding-large">
                <div v-for="(order, index) in cart" class="uk--box">
                    <div class="uk--box-body-with-padding">
                        <div class="uk-grid-collapse" uk-grid>
                            <div class="uk-width-2-5">
                                <span class="uk--box-label">Order Code</span>
                                <span class="uk--box-text">-</span>
                            </div>
                            <div class="uk-width-1-5">
                                    <span class="uk--box-label">Package</span>
                                    <span class="uk--box-text">@{{ order.package | packageForHuman(packages) }}</span>
                                </div>
                            <div class="uk-width-1-5">
                                <span class="uk--box-label">Date</span>
                                <span class="uk--box-text">@{{ order.date }}</span>
                            </div>
                            <div class="uk-width-1-5">
                                <span class="uk--box-label">Status</span>
                                <span class="uk--box-text">-</span>
                            </div>
                        </div>
                        <div class="uk-grid-collapse uk-margin-top" uk-grid>
                            <div class="uk-width-2-5">
                                <span class="uk--box-label" :class="{ 'uk-text-danger': inputErrors.customer }">Customer</span>
                                <span class="uk--box-text">@{{ input.customerString }}</span>
                            </div>
                            <div v-for="(val, key) in order.detail" class="uk-width-1-5">
                                <span class="uk--box-label">@{{ key | humanKey }}</span>
                                <span class="uk--box-text">@{{ val }}</span>
                            </div>
                        </div>
                        <div class="uk-grid-collapse uk-margin-top" uk-grid>
                            <div class="uk-width-2-5">
                                <span class="uk--box-label" :class="{ 'uk-text-danger': inputErrors[`orders.${index}.location`] }">Location</span>
                                <span class="uk--box-text">@{{ order.location }}</span>
                            </div>
                            <div class="uk-width-2-5">
                                <span class="uk--box-label" :class="{ 'uk-text-danger': inputErrors[`orders.${index}.region`] }">Region</span>
                                <span class="uk--box-text">@{{ order.region | regionForHuman(options.region) }}</span>
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
                                    <td class="uk-text-right">@{{ item.price }}</td>
                                    <td class="uk-text-right">@{{ item.quantity }}</td>
                                    <td class="uk-text-right">@{{ item.price * item.quantity }}</td>
                                </tr>
                            </tbody>
                            <tfooter>
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td class="uk-text-right"></td>
                                    <td class="uk-text-right">@{{ order.items | totalPrice }}</td>
                                </tr>
                            </tfooter>
                        </table>
                    </div>
                </div>
                <div class="uk-margin uk-text-center">
                    <el-button class="uk-text-uppercase" type="success" round @click="submitOrder">Submit Order</el-button>
                </div>
            </div>
        </div>
    </div>
</div>