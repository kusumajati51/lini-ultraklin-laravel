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
            <i class="fas fa-users fa-lg" />
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">
            DOWNLINE
            <sup v-if="pagination.total > 0" class="el-badge__content">
              {{ pagination.total }}
            </sup>
          </h3>
        </div>
      </div>
    </div>
    <div class="uk-card-body">
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
        <div class="uk-width-auto">
          <el-input
            v-model="filter.min_order"
            type="number"
            placeholder="Minimal order"
          >
            <el-button slot="prepend">Minimal Order</el-button>
          </el-input>
        </div>
        <div class="uk-width-expand">
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
      </div>
      <div class="uk-margin uk-text-right">
        <el-button type="success" @click="exportToExcel">
          <i class="fas fa-file-excel" />
        </el-button>
        <el-button type="primary" @click="changeFilter">Filter</el-button>
      </div>
      <div
        v-if="pagination.total > 0"
        class="uk-margin-large-top uk-overflow-auto"
      >
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th class="uk-text-center" width="150">Total Order</th>
              <th width="150">Register At</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id">
              <td>{{ u.name }}</td>
              <td>{{ u.email }}</td>
              <td>{{ u.phone }}</td>
              <td class="uk-text-center">{{ u.total_order }}</td>
              <td>{{ u.created_at }}</td>
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
      users: [],
      pagination: {},
      filter: {
        page: 1,
        time: [],
        search: null,
        min_order: null,
        region: []
      },
      options: {
        region: []
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
      this.getUsers()
    },
    onRouteChanged() {
      this.getUsers()
    },
    onPageChanged(page) {
      this.filter.page = page

      this.getUsers()
    },
    changeFilter() {
      this.$router.push({
        query: this.filter
      })
    },
    setFilter() {
      let query = this.$route.query

      if (query.page) {
        this.filter.page = query.page
      }

      if (query.time) {
        this.filter.time = query.time
      }

      if (query.search) {
        this.filter.search = query.search
      }

      if (query.min_order) {
        this.filter.min_order = query.min_order
      }

      if (query.region) {
        if (typeof query.region === 'string') {
          this.filter.region.push(query.region)
        } else {
          this.filter.region = query.region
        }
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
    getUsers() {
      this.$http
        .get(`/admin/v1/json/sales/${this.$route.params.code}/downline`, {
          params: this.filter
        })
        .then(res => {
          this.users = res.data.data
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
        .get(`/admin/v1/json/sales/${this.$route.params.code}/downline`, {
          params,
          responseType: 'blob'
        })
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
