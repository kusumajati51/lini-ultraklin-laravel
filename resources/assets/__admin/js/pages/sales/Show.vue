<template>
  <div>
    <div class="uk-grid-match uk-grid-small" uk-grid>
      <div class="uk-width-3-5">
        <div class="uk-card uk-card-default uk-card-small">
          <div class="uk-card-body" />
        </div>
      </div>
      <div class="uk-width-2-5">
        <div class="uk-card uk-card-default uk-card-small">
          <div class="uk-card-header app--card-header-tool">
            <div class="uk-grid-collapse" uk-grid>
              <div class="uk-width-auto">
                <div class="app--card-header-tool-icon app--border-right">
                  <i class="fas fa-user-tie fa-lg" />
                </div>
              </div>
              <div class="uk-width-expand">
                <h3 class="app--card-title">PROFILE</h3>
              </div>
            </div>
          </div>
          <div class="uk-card-body">
            <ul class="uk-list uk-list-divider">
              <li>
                <div>
                  <div class="app--list-label">Name</div>
                  <div class="app--list-text">{{ user.name }}</div>
                </div>
              </li>
              <li>
                <div>
                  <div class="app--list-label">Email</div>
                  <div class="app--list-text">{{ user.email }}</div>
                </div>
              </li>
              <li>
                <div>
                  <div class="app--list-label">Phone</div>
                  <div class="app--list-text">{{ user.phone }}</div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="uk-card uk-card-default uk-card-small uk-margin">
      <div class="uk-card-header app--card-header-tool">
        <div class="uk-grid-collapse" uk-grid>
          <div class="uk-width-auto">
            <div class="app--card-header-tool-icon app--border-right">
              <i class="fas fa-users fa-lg" />
            </div>
          </div>
          <div class="uk-width-expand">
            <h3 class="app--card-title">LASTEST DOWNLINE</h3>
          </div>
          <div class="uk-width-auto">
            <div class="app--card-header-tool-button app--border-left">
              <router-link :to="{ name: 'sales-downline' }">
                <i class="fas fa-ellipsis-h" />
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="uk-card-body">
        <div class="uk-overflow-auto">
          <table class="uk-table uk-table-divider uk-table-small uk-text-small">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in downline" :key="u.id">
                <td>{{ u.name }}</td>
                <td>{{ u.email }}</td>
                <td>{{ u.phone }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="uk-card uk-card-default uk-card-small uk-margin">
      <div class="uk-card-header app--card-header-tool">
        <div class="uk-grid-collapse" uk-grid>
          <div class="uk-width-auto">
            <div class="app--card-header-tool-icon app--border-right">
              <i class="fas fa-file-alt fa-lg" />
            </div>
          </div>
          <div class="uk-width-expand">
            <h3 class="app--card-title">LASTEST DOWNLINE ORDERS</h3>
          </div>
          <div class="uk-width-auto">
            <div class="app--card-header-tool-button app--border-left">
              <router-link :to="{ name: 'sales-downline-order' }">
                <i class="fas fa-ellipsis-h" />
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="uk-card-body">
        <div class="uk-overflow-auto">
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
              <tr v-for="order in downlineOrders" :key="order.id">
                <td>
                  <router-link
                    :to="{
                      name: 'sales-downline-order-show',
                      params: {
                        code: $route.params.code,
                        orderCode: order.code
                      }
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
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {},
      downline: [],
      downlineOrders: []
    }
  },

  created() {
    this.getUser()
    this.getDownline()
    this.getDownlineOrders()
  },

  methods: {
    getUser() {
      this.$http
        .get(`/admin/v1/json/sales/${this.$route.params.code}`)
        .then(res => {
          if (res.data.error) {
            this.$notify({
              title: 'ERROR',
              message: res.data.message,
              type: 'error'
            })

            this.$router.push({
              name: 'sales'
            })

            return
          }

          this.user = res.data
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    getDownline() {
      this.$http
        .get(`/admin/v1/json/sales/${this.$route.params.code}/downline`, {
          params: {
            limit: 10
          }
        })
        .then(res => {
          this.downline = res.data.data
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    getDownlineOrders() {
      this.$http
        .get(
          `/admin/v1/json/sales/${this.$route.params.code}/downline/orders`,
          {
            params: {
              limit: 10
            }
          }
        )
        .then(res => {
          this.downlineOrders = res.data.data
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    }
  }
}
</script>
