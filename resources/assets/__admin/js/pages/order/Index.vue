<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-icon app--border-right">
            <i class="fas fa-file-alt fa-lg" />
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">
             ORDERS
            <sup v-if="pagination.total > 0" class="el-badge__content">
              {{ pagination.total }}
            </sup>
          </h3>
        </div>
      </div>
    </div>
    <div class="uk-card-body">
      <div>
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-auto">
            <div>
              <el-date-picker
                v-model="filter.time"
                type="daterange"
                value="yyyy-MM-dd"
                value-format="yyyy-MM-dd"
                start-placeholder="Start date"
                end-placeholder="End date">
              </el-date-picker>
            </div>
          </div>
          <div class="uk-width-expand">
            <el-input v-model="filter.search" placeholder="Search">
              <el-button slot="append" @click="getOrders">
                <i class="fas fa-search" />
              </el-button>
            </el-input>
          </div>
        </div>
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-2-3">
            <el-select
              v-model="filter.status"
              class="uk-width-1-1"
              placeholder="Status"
              multiple>
              <el-option
                v-for="(item, index) in options.status"
                :key="index"
                :label="item"
                :value="item">
              </el-option>
            </el-select>
          </div>
          <div class="uk-width-1-3">
            <el-select
              v-model="filter.user_status"
              class="uk-width-1-1"
              placeholder="User status"
              multiple>
              <el-option
                v-for="item in options.userStatus"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
          </div>
        </div>
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-2-3">
            <el-select
              v-model="filter.region"
              class="uk-width-1-1"
              placeholder="Region"
              multiple>
              <el-option
                v-for="item in regions"
                :key="item.code"
                :label="item.name"
                :value="item.code">
              </el-option>
            </el-select>
          </div>
          <div class="uk-width-1-3">
            <el-select
              v-model="filter.order_source"
              class="uk-width-1-1"
              placeholder="Order source"
              multiple>
              <el-option
                v-for="item in options.orderSource"
                :key="item"
                :label="item"
                :value="item">
              </el-option>
            </el-select>
          </div>
        </div>
        <div class="uk-margin uk-text-right">
          <el-button type="success" @click="exportToExcel">
            <i class="fas fa-file-excel" />
          </el-button>
          <el-button type="primary" @click="getOrders">Filter</el-button>
        </div>
      </div>
      <div v-if="pagination.total > 1" class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
          <thead>
            <tr>
              <th>Code</th>
              <th>User</th>
              <th>Package</th>
              <th>Referral</th>
              <th class="uk-text-center" width="100">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td>
                <router-link
                  :to="{ name: 'order-show', params: { code: order.code } }"
                  >{{ order.code }}</router-link
                >
              </td>
              <td>{{ order.user_name }}</td>
              <td>{{ order.package_display_name }}</td>
              <td>{{ order.referral }}</td>
              <td class="uk-text-center">
                <el-tag
                  v-if="order.status.toLowerCase() === 'done'"
                  type="success"
                  size="small">
                  {{ order.status }}
                </el-tag>
                <el-tag
                  v-else-if="order.status.toLowerCase() === 'pending'"
                  type="warning"
                  size="small">
                  {{ order.status }}
                </el-tag>
                <el-tag
                  v-else-if="order.status.toLowerCase() === 'cancel'"
                  type="danger"
                  size="small">
                  {{ order.status }}
                </el-tag>
                <el-tag
                  v-else type="primary"
                  size="small">
                  {{ order.status }}
                </el-tag>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else>
        <no-content></no-content>
      </div>
    </div>
    <div class="uk-card-footer">
      <el-pagination
        v-if="pagination.total > 0"
        :page-count="parseInt(pagination.last_page)"
        :page-size="parseInt(pagination.per_page)"
        :total="parseInt(pagination.total)"
        layout="prev, pager, next"
        class="uk-text-center"
        @current-change="onPageChanged">
      </el-pagination>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'

import NoContent from '../../components/NoContent'

export default {
  components: {
    NoContent
  },

  data () {
    return {
      orders: [],
      pagination: {},
      options: {
        status: [
          'Cancel',
          'Pending',
          'Confirm',
          'On The Way',
          'Process',
          'Done'
        ],
        userStatus: [
          {
            value: 'user',
            label: 'User'
          },
          {
            value: 'sales',
            label: 'Sales'
          },
          {
            value: 'partner',
            label: 'Partner'
          },
          {
            value: 'agent',
            label: 'Agent'
          },
          {
            value: 'tester',
            label: 'Tester'
          }
        ],
        orderSource: ['Online', 'Offline']
      },
      filter: {
        page: 1,
        time: [],
        status: [],
        user_status: [],
        order_source: [],
        region: [],
        search: null
      }
    }
  },

  computed: {
    ...mapState([
      'regions'
    ])
  },

  async created () {
    if (this.regions.length === 0) await this.$store.dispatch('getRegions')

    await this.setFilter()

    this.getOrders()
  },

  methods: {
    onPageChanged (page) {
      this.filter.page = page

      this.getOrders()
    },
    setFilter () {
      let query = this.$route.query

      this.filter.time = [this.$date().today(), this.$date().today()]
      this.filter.status = [
        'Cancel',
        'Pending',
        'Confirm',
        'On The Way',
        'Process',
        'Done'
      ]
      this.filter.user_status = ['user']
      this.filter.order_source = ['Online']
      this.filter.region = this.regions.map(region => {
        return region.code
      })

      if (query.page) {
        this.filter.page = query.page
      }

      if (query.time) {
        this.filter.time = query.time
      }

      if (query.status) {
        this.filter.status = []

        if (typeof query.status === 'string') {
          this.filter.status.push(query.status)
        } else {
          this.filter.status = query.status
        }
      }

      if (query.user_status) {
        this.filter.user_status = []

        if (typeof query.user_status === 'string') {
          this.filter.user_status.push(query.user_status)
        } else {
          this.filter.user_status = query.user_status
        }
      }

      if (query.order_source) {
        this.filter.order_source = []

        if (typeof query.order_source === 'string') {
          this.filter.order_source.push(query.order_source)
        } else {
          this.filter.order_source = query.order_source
        }
      }

      if (query.region) {
        if (typeof query.region === 'string') {
          this.filter.region.push(query.region)
        } else {
          this.filter.region = query.region
        }
      }

      if (query.search) {
        this.filter.search = query.search
      }
    },
    updateRoute () {
      this.$router.push({
        query: this.filter
      })
    },
    async getOrders () {
      try {
        let res = await this.$service.order.get(this.filter)

        this.orders = res.data.data
        this.pagination = res.data

        delete this.pagination.data

        this.updateRoute()
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    },
    async exportToExcel () {
      let params = Object.assign({}, this.filter)

      params['export'] = 1

      try {
        let res = await this.$service.order.export(params)

        let blob = window.URL.createObjectURL(new Blob([res.data]))
        let link = document.createElement('a')
        let filename = res.headers['content-disposition'].match(/"(.*)"/)[1]

        link.style.cssText = 'visibility:hidden;'
        link.href = blob

        link.setAttribute('download', filename)
        link.click()
        link.remove()
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    }
  }
}
</script>
