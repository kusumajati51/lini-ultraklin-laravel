<template>
  <div class="uk-container uk-container-expand">
    <div class="uk-margin-top uk-margin-bottom">
      <div class="uk-card uk-card-default uk-card-small">
        <div class="uk-card-header">
          <h3 class="uk-card-title">
            <span>{{ this.$route.params.name.toUpperCase() }}</span>
            <sup class="el-badge__content">{{ filteredLogs.length }}</sup>
          </h3>
        </div>
        <div class="uk-card-body">
          <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto">
              <el-date-picker v-model="filter.date" value-format="yyyy-MM-dd" @change="fetchLogs($route.params.name)" />
            </div>
            <div class="uk-width-medium">
              <el-select v-model="filter.code" class="uk-width-1-1" multiple>
                <el-option v-for="code in options.code" :key="code" :value="code" :label="code" />
              </el-select>
            </div>
            <div class="uk-width-auto">
              <el-button @click="fetchLogs($route.params.name)">Refresh</el-button>
            </div>
          </div>
          <div class="uk-overflow-auto uk-margin-large-top">
            <table class="uk-table uk-table-divider uk-table-small uk-table-middle uk-text-small">
              <thead>
                <th></th>
                <th width="150">Date</th>
                <th class="uk-text-center">Status</th>
                <th class="uk-text-center">Method</th>
                <th>URL</th>
                <th>User</th>
              </thead>
              <tbody>
                <template v-for="(log, index) in filteredLogs">
                  <tr :key="index">
                    <td class="app--table_expand-button" @click.prevent="collapseToggle(index)">
                      <a href="#">
                        <i v-if="log.collapse" class="fas fa-angle-right"></i>
                        <i v-else class="fas fa-angle-down"></i>
                      </a>
                    </td>
                    <td>{{ log.datetime.date | date }}</td>
                    <td class="uk-text-center">
                      <span class="uk-label" :class="log.context.responseStatus | labelStatus">
                        {{ log.context.responseStatus }}
                      </span>
                    </td>
                    <td class="uk-text-center">{{ log.context.method }}</td>
                    <td>
                      <p>{{ log.context.fullUrl }}</p>
                      <p>{{ log.context.userAgent }}</p>
                    </td>
                    <td>{{ log.context.user }}</td>
                  </tr>
                  <tr v-if="!log.collapse" :key="`d${index}`">
                    <td colspan="6">
                      <div>
                        <h5 class="uk-margin-remove">Request</h5>
                        <pre class="app--code-dark">{{ log.context.requestBody }}</pre>
                      </div>
                      <div>
                        <h5 class="uk-margin-remove">Response</h5>
                        <pre class="app--code-dark">{{ log.context.responseData }}</pre>
                      </div>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
let date = new Date()
let year = date.getFullYear()
let month = ('0' + (date.getMonth() + 1)).slice(-2)
let day = ('0' + date.getDate()).slice(-2)

export default {
  data () {
    return {
      logs: [],
      filter: {
        date: `${year}-${month}-${day}`,
        code: []
      },
      options: {
        code: []
      }
    }
  },
  computed: {
    filteredLogs () {
      let self = this

      return this.logs.filter(item => {
        return self.filter.code.includes(item.context.responseStatus)
      })
    }
  },
  methods: {
    fetchLogs (name) {
      this.options.code = []
      this.filter.code = []

      this.$http.get(`/system-tools/json/logs/${name}?date=${this.filter.date}`)
        .then(response => {
          this.logs = response.data.reverse().map(item => {
            item.collapse = true

            return item
          })

          this.logs.forEach(item => {
            if (!this.options.code.includes(item.context.responseStatus)) {
              this.options.code.push(item.context.responseStatus)
              this.filter.code.push(item.context.responseStatus)
            }
          })
        })
        .catch(error => {
          console.log(error.message)
        })
    },
    collapseToggle (index) {
      this.filteredLogs[index].collapse = !this.filteredLogs[index].collapse
    }
  },
  filters: {
    date (val) {
      let timestamp = Date.parse(val)
      let date = new Date(timestamp)
      let year = date.getFullYear()
      let month = ('0' + (date.getMonth() + 1)).slice(-2)
      let day = ('0' + date.getDate()).slice(-2)
      let hours = ('0' + date.getHours()).slice(-2)
      let minutes = ('0' + date.getMinutes()).slice(-2)

      return `${year}-${month}-${day} ${hours}:${minutes}`
    },
    labelStatus (val) {
      let code = val.toString()
      let status = ''

      if (code.match(/^2.+/g)) {
        status = 'uk-label-success'
      } else if (code.match(/^4.+/g)) {
        status = 'uk-label-warning'
      } else {
        status = 'uk-label-danger'
      }

      return status
    }
  },
  created () {
    this.fetchLogs(this.$route.params.name)
  }
}
</script>
