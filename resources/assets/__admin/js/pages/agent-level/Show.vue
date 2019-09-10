<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-button app--border-right">
            <router-link :to="{ name: 'agent-level' }">
              <i class="fas fa-angle-left fa-lg"></i>
            </router-link>
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">{{ agentLevel.name }}</h3>
        </div>
      </div>
    </div>
    <div class="uk-card-body">
      <p v-if="agentLevel.description && agentLevel.description.length > 0">
        {{ agentLevel.description }}
      </p>
      <div class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
          <thead>
            <tr>
              <th>Service</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in agentLevel.services" :key="service.id">
              <td>{{ service.display_name }}</td>
              <td>
                <span>{{
                  service.pivot.value
                    | currency('', 0, {
                      thousandsSeparator: '.',
                      decimalSeparator: ','
                    })
                }}</span>
                <span v-if="service.pivot.percent">%</span>
                <span v-else>IDR</span>
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
      agentLevel: {},
      error: false,
      errorMessage: ''
    }
  },

  created() {
    this.getAgentLevel()
  },

  methods: {
    getAgentLevel() {
      this.error = false
      this.errorMessage = ''

      this.$http
        .get(`/admin/v1/json/agent-levels/${this.$route.params.id}`)
        .then(res => {
          this.agentLevel = res.data
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    }
  }
}
</script>
