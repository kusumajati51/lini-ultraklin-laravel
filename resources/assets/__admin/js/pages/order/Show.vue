<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-button app--border-right">
            <a href="#" @click.prevent="$router.go(-1)">
              <i class="fas fa-angle-left fa-lg" />
            </a>
          </div>
        </div>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-icon app--border-right">
            <i class="fas fa-info fa-lg" />
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">ORDER DETAIL</h3>
        </div>
      </div>
    </div>
    <div class="uk-card-body">
      <div>
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-2-5">
            <span class="app--list-label">Order Code</span>
            <span class="app--list-text">
              {{ order.code }}
              <el-tag
              v-if="order.prices.final_total === 0"
              class="uk-margin-small-left"
              type="success"
              size="mini">
                FREE
              </el-tag>
            </span>
          </div>
          <div class="uk-width-1-5">
            <span class="app--list-label">Package</span>
            <span class="app--list-text">{{ (order.package) ? order.package.display_name : '' }}</span>
          </div>
          <div class="uk-width-1-5">
            <span class="app--list-label">Date</span>
            <span class="app--list-text">{{ order.date }}</span>
          </div>
          <div class="uk-width-1-5">
            <span class="app--list-label">Status</span>
            <span class="app--list-text">
              <el-tag
              :type="tagStatus[order.status]"
              size="mini">
                {{ order.status }}
              </el-tag>
            </span>
          </div>
        </div>
        <div class="uk-grid-small uk-margin-top" uk-grid>
          <div class="uk-width-3-5">
            <span class="app--list-label">Location</span>
            <span class="app--list-text">{{ order.location }}</span>
          </div>
          <div v-if="order.invoice" class="uk-width-1-5">
            <span class="app--list-label">Customer</span>
            <span class="app--list-text">{{ order.user.name }}</span>
          </div>
          <div v-if="order.invoice" class="uk-width-1-5">
            <span class="app--list-label">Phone</span>
            <span class="app--list-text">{{ order.user.phone }}</span>
          </div>
        </div>
        <div v-if="order.detail" class="uk-grid-small uk-margin-top" uk-grid>
          <!-- CLEANING -->
          <div v-if="order.detail.building_type" class="uk-width-1-5">
            <span class="app--list-label">Building Type</span>
            <span class="app--list-text">{{ order.detail.building_type }}</span>
          </div>
          <div v-if="order.detail.pets" class="uk-width-1-5">
            <span class="app--list-label">Pets</span>
            <span class="app--list-text">{{ order.detail.pets }}</span>
          </div>
          <div v-if="order.detail.cso_gender" class="uk-width-1-5">
            <span class="app--list-label">CSO Gender</span>
            <span class="app--list-text">{{ order.detail.cso_gender }}</span>
          </div>
          <div v-if="order.detail.total_cso" class="uk-width-1-5">
            <span class="app--list-label">Total CSO</span>
            <span class="app--list-text">{{ order.detail.total_cso }}</span>
          </div>
          <div v-if="order.detail.room" class="uk-width-1-5">
            <span class="app--list-label">Room</span>
            <span class="app--list-text">
              <ul class="uk-list uk-margin-remove">
                <li v-for="(room, index) in order.detail.room" :key="index" class="uk-margin-remove">
                  {{ room }}
                </li>
              </ul>
            </span>
          </div>
          <!-- LAUNDRY -->
          <div v-if="order.detail.fragrance" class="uk-width-1-5">
            <span class="app--list-label">Fragrance</span>
            <span class="app--list-text">{{ order.detail.fragrance }}</span>
          </div>
          <div v-if="order.detail.total_items" class="uk-width-1-5">
            <span class="app--list-label">Total Items</span>
            <span class="app--list-text">{{ order.detail.total_items }}</span>
          </div>
          <div v-if="order.detail.delivery_date" class="uk-width-1-5">
            <span class="app--list-label">Delivery Date</span>
            <span class="app--list-text">{{ order.detail.delivery_date }}</span>
          </div>
        </div>
        <div class="uk-grid-small uk-margin-top" uk-grid>
          <div class="uk-width-expand">
            <span class="app--list-label">Note</span>
            <span class="app--list-text">{{ order.note }}</span>
          </div>
        </div>
        <table class="uk-table uk-table-small uk-table-divider uk-text-small">
          <thead>
            <tr>
              <th>Item</th>
              <th class="uk-text-right" width="100px">Price</th>
              <th class="uk-text-right" width="100px">Quantity</th>
              <th class="uk-text-right" width="100px">Sub Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in order.items" :key="index">
              <td>{{ item.name }}</td>
              <td class="uk-text-right">{{ item.human_prices.price }}</td>
              <td class="uk-text-right">{{ item.pivot.quantity }}</td>
              <td class="uk-text-right">{{ item.human_prices.total }}</td>
            </tr>
            <tr v-if="order.extra_price_cso > 0">
              <td>Additional CSO</td>
              <td class="uk-text-right">{{ order.human_prices.amount }}</td>
              <td class="uk-text-right">{{ order.human_prices.additional_cso }}</td>
              <td class="uk-text-right">{{ order.human_prices.extra_price_cso }}</td>
            </tr>
          </tbody>
          <tbody>
            <tr>
              <td colspan="4" />
            </tr>
            <tr>
              <td class="uk-text-bold" colspan="3">SUB TOTAL</td>
              <td class="uk-text-right">{{ order.human_prices.sub_total }}</td>
            </tr>
            <tr>
              <td class="uk-text-bold" colspan="3">DISCOUNT</td>
              <td class="uk-text-right">{{ order.human_prices.discount }}</td>
            </tr>
            <tr>
              <td class="uk-text-bold" colspan="3">TOTAL</td>
              <td class="uk-text-bold uk-text-right">{{ order.human_prices.final_total }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="uk-card-footer">
      <template v-if="order.store_id === null">
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-expand">
            <el-radio-group
            v-model="order.inputStatus"
            :disabled="!order.edit"
            size="small">
              <el-radio-button label="Cancel" />
              <el-radio-button label="Pending" />
              <el-radio-button label="Confirm" />
              <el-radio-button label="On The Way" />
              <el-radio-button label="Process" />
              <el-radio-button label="Done" />
            </el-radio-group>
          </div>
          <div class="uk-width-auto">
            <el-button
              v-if="order.edit"
              type="danger"
              size="small"
              @click="cancelEdit">
              Cancel
            </el-button>
            <el-button
              v-if="order.edit"
              type="success"
              size="small"
              :loading="order.editLoading"
              @click="update">
              Done
            </el-button>
            <el-button
              v-else
              type="primary"
              size="small"
              @click="edit">
              Edit
            </el-button>
          </div>
        </div>
        <hr>
      </template>
      <div class="app--list-label">CREATED BY</div>
      <div class="app--list-text">{{ order.created_by }}</div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      order: {
        prices: {},
        human_prices: {}
      },
      tagStatus: {
        Paid: 'success',
        Unpaid: 'danger',
        Cancel: 'danger',
        Pending: 'warning',
        Confirm: '',
        'On The Way': '',
        Process: '',
        Done: 'success'
      }
    }
  },

  created() {
    this.getOrder()
  },

  methods: {
    async getOrder () {
      try {
        let res = null

        if (this.$route.name === 'order-show') {
          res = await this.$service.order.find(this.$route.params.code)
        } else {
          res = await this.$service.order.findByUpline(this.$route.params.code, this.$route.params.orderCode)
        }

        this.order = {
          ...res.data,
          ...{
            inputStatus: res.data.status,
            edit: false,
            editLoading: false
          }
        }
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    },
    edit() {
      this.order.edit = true
    },
    cancelEdit() {
      this.order.edit = false
    },
    async update () {
      this.order.edit = false
      this.order.editLoading = true

      try {
        let res = await this.$service.order.updateStatus(this.order.code, this.order.inputStatus)

        this.$notify({
          title: 'SUCCESS',
          message: res.data.message,
          type: 'success'
        })

        this.order.status = this.order.inputStatus
        this.order.editLoading = false
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }

      this.order.editLoading = false
    }
  }
}
</script>
