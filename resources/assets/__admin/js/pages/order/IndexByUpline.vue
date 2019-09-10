<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-button app--border-right">
            <router-link
              :to="{
                name: 'sales-show',
                params: { code: this.$route.params.code }
              }"
            >
              <i class="fas fa-angle-left fa-lg" />
            </router-link>
          </div>
        </div>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-icon app--border-right">
            <i class="fas fa-file-alt fa-lg" />
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">
            DOWNLINE ORDERS
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
                end-placeholder="End date"
              >
              </el-date-picker>
            </div>
          </div>
          <div class="uk-width-expand">
            <el-input v-model="filter.search" placeholder="Search">
              <el-button slot="append" @click="changeFilter">
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
              multiple
            >
              <el-option
                v-for="(item, index) in options.status"
                :key="index"
                :label="item"
                :value="item"
              />
            </el-select>
          </div>
          <div class="uk-width-1-3">
            <el-select
              v-model="filter.user_status"
              class="uk-width-1-1"
              placeholder="User status"
              multiple
            >
              <el-option
                v-for="item in options.userStatus"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
          </div>
        </div>
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-2-3">
            <el-select
              v-model="filter.region"
              class="uk-width-1-1"
              placeholder="Region"
              multiple
            >
              <el-option
                v-for="item in options.region"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
          </div>
          <div class="uk-width-1-3">
            <el-select
              v-model="filter.order_source"
              class="uk-width-1-1"
              placeholder="Order source"
              multiple
            >
              <el-option
                v-for="item in options.orderSource"
                :key="item"
                :label="item"
                :value="item"
              />
            </el-select>
          </div>
        </div>
        <div class="uk-margin uk-text-right">
          <el-button type="success" @click="exportToExcel">
            <i class="fas fa-file-excel" />
          </el-button>
          <el-button type="primary" @click="changeFilter">Filter</el-button>
        </div>
      </div>
      <div v-if="pagination.total > 1" class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
          <thead>
            <tr>
              <th>Code</th>
              <th>User</th>
              <th>Package</th>
              <th class="uk-text-center" width="100">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td>
                <router-link
                  :to="{
                    name: 'sales-downline-order-show',
                    params: { code: $route.params.code, orderCode: order.code }
                  }"
                >
                  {{ order.code }}
                </router-link>
              </td>
              <td>{{ order.user_name }}</td>
              <td>{{ order.package_display_name }}</td>
              <td class="uk-text-center">
                <el-tag
                  v-if="order.status.toLowerCase() === 'done'"
                  type="success"
                  size="small"
                >
                  {{ order.status }}
                </el-tag>
                <el-tag
                  v-else-if="order.status.toLowerCase() === 'pending'"
                  type="warning"
                  size="small"
                >
                  {{ order.status }}
                </el-tag>
                <el-tag
                  v-else-if="order.status.toLowerCase() === 'cancel'"
                  type="danger"
                  size="small"
                >
                  {{ order.status }}
                </el-tag>
                <el-tag v-else type="primary" size="small">
                  {{ order.status }}
                </el-tag>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else><no-content /></div>
    </div>
    <div class="uk-card-footer">
      <el-pagination
        v-if="pagination.total > 0"
        :page-count="parseInt(pagination.last_page)"
        :page-size="parseInt(pagination.per_page)"
        :total="parseInt(pagination.total)"
        layout="prev, pager, next"
        class="uk-text-center"
        @current-change="onPageChanged"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

import NoContent from '../../components/NoContent'

export default {
  components: {
    NoContent
  },
  data() {
    return {
      orders: [],
      pagination: {},
      options: {
        status: ['Cancel', 'Pending', 'Confirm', 'On The Way', 'Done'],
        userStatus: [
          {
            value: 'user',
            label: 'User'
          },
          {
            value: 'tester',
            label: 'Tester'
          }
        ],
        orderSource: ['Online'],
        region: []
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
    ...mapGetters(['appInitialized'])
  },

  watch: {
    appInitialized: 'onAppInitialized',
    $route: 'onRouteChanged'
  },

  created() {
    this.onAppInitialized()
  },

  methods: {
    onAppInitialized() {
      this.setFilter()
      this.setOptions()
    },
    onRouteChanged() {
      this.getOrders()
    },
    onPageChanged(page) {
      this.filter.page = page

      this.getOrders()
    },
    setFilter() {
      let query = this.$route.query

      this.filter.time = [this.$date().today(), this.$date().today()]
      this.filter.status = [
        'Cancel',
        'Pending',
        'Confirm',
        'On The Way',
        'Done'
      ]
      this.filter.user_status = ['user']
      this.filter.order_source = ['Online']
      this.filter.region = this.$store.state.regions.map(region => {
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

      if (query.userStatus) {
        this.filter.user_status = []

        if (typeof query.userStatus === 'string') {
          this.filter.user_status.push(query.userStatus)
        } else {
          this.filter.user_status = query.userStatus
        }
      }

      if (query.orderSource) {
        this.filter.order_source = []

        if (typeof query.orderSource === 'string') {
          this.filter.order_source.push(query.orderSource)
        } else {
          this.filter.order_source = query.orderSource
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
    setOptions() {
      this.options.region = this.$store.state.regions.map(region => {
        return {
          value: region.code,
          label: region.name
        }
      })
    },
    changeFilter() {
      this.$router.push({
        query: this.filter
      })
    },
    getOrders() {
      this.$http
        .get(
          `/admin/v1/json/sales/${this.$route.params.code}/downline/orders`,
          {
            params: this.filter
          }
        )
        .then(res => {
          this.orders = res.data.data
          this.pagination = res.data

          delete this.pagination.data
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    exportToExcel() {
      let params = Object.assign({}, this.filter)

      params['export'] = 1

      this.$http
        .get(
          `/admin/v1/json/sales/${this.$route.params.code}/downline/orders`,
          {
            params,
            responseType: 'blob'
          }
        )
        .then(res => {
          let blob = window.URL.createObjectURL(new Blob([res.data]))
          let link = document.createElement('a')
          let filename = res.headers['content-disposition'].match(/"(.*)"/)[1]

          link.style.cssText = 'visibility:hidden;'
          link.href = blob

          link.setAttribute('download', filename)
          link.click()
          link.remove()
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    }
  }
}
</script>
