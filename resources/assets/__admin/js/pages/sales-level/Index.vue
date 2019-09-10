<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-icon app--border-right">
            <i class="fas fa-award fa-lg" />
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">SALES LEVELS</h3>
        </div>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-button app--border-left">
            <router-link :to="{ name: 'sales-level-create' }">
              <i class="fas fa-plus" />
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
              <th>Description</th>
              <th class="uk-text-center" width="50px">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="level in levels.data" :key="level.id">
              <td>
                <router-link
                  :to="{ name: 'sales-level-show', params: { id: level.id } }"
                >
                  {{ level.name }}
                </router-link>
              </td>
              <td>{{ level.desription }}</td>
              <td class="uk-text-center">
                <router-link
                  :to="{ name: 'sales-level-edit', params: { id: level.id } }"
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
      levels: [],
      error: false,
      errorMessage: ''
    }
  },

  created() {
    this.fetchAgentLevels()
  },

  methods: {
    fetchAgentLevels() {
      this.error = false
      this.errorMessage = ''

      this.$http
        .get('/admin/v1/json/sales-levels', {
          params: {}
        })
        .then(res => {
          this.levels = res.data
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    }
  }
}
</script>
