<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-icon app--border-right">
            <i class="fas fa-user-tie fa-lg"></i>
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">SALES</h3>
        </div>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-button app--border-left">
            <router-link :to="{ name: 'sales-create' }">
              <i class="fas fa-plus"></i>
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
              <th>Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Level</th>
              <th class="uk-text-center" width="50px">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in sales" :key="user.id">
              <td>{{ user.code }}</td>
              <td>
                <router-link
                  :to="{ name: 'sales-show', params: { code: user.code } }"
                >
                  {{ user.name }}
                </router-link>
              </td>
              <td>{{ user.phone }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.level === null ? '' : user.level.name }}</td>
              <td class="uk-text-center">
                <router-link
                  :to="{ name: 'sales-edit', params: { id: user.id } }"
                >
                  <i class="fas fa-edit" />
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      sales: [],
      filter: {
        sort: ['name', 'asc']
      },
      pagination: {},
      error: false,
      errorMessage: ''
    }
  },

  created() {
    this.fetchSales()
  },

  methods: {
    async fetchSales () {
      this.error = false
      this.errorMessage = ''

      try {
        let res = await this.$service.getSales(this.filter)

        this.sales = res.data.data
        this.filter = res.data.filter

        Object.keys(res.data).forEach((key, index) => {
          if (key !== 'data' && key !== 'filter') {
            this.pagination[key] = res.data[key]
          }
        })
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    }
  }
}
</script>
