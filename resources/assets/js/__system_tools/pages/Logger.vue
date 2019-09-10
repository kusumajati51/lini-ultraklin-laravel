<template>
  <div class="uk-container">
    <div class="uk-margin-top uk-margin-bottom">
      <div class="uk-card uk-card-default uk-card-small">
        <div class="uk-card-header">
          <h3 class="uk-card-title">
            <span>LOGS</span>
            <sup class="el-badge__content">{{ filteredLogs.length }}</sup>
          </h3>
        </div>
        <div class="uk-card-body">
          <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto">
              <el-date-picker v-model="filter.date" value-format="yyyy-MM-dd" @change="getLogs" />
            </div>
            <div class="uk-width-auto">
              <el-select v-model="filter.code" multiple>
                <el-option v-for="code in options.code" :key="code" :value="code" :label="code" />
              </el-select>
            </div>
          </div>
          <div class="uk-overflow-auto uk-margin-large-top">
            <table class="uk-table uk-table-divider uk-table-small uk-table-middle uk-text-small">
              <thead>
                <th>Date</th>
                <th class="uk-text-center">Status</th>
                <th class="uk-text-center">Method</th>
                <th>URL</th>
                <th>User</th>
              </thead>
              <tbody>
                <tr v-for="(log, index) in filteredLogs" :key="index">
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
    getLogs () {
      this.options.code = []
      this.filter.code = []

      this.$http.get(`/admin/json/system-tools/logger?date=${this.filter.date}`)
        .then(response => {
          this.logs = response.data.reverse()

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
    codeToStatus () {

    }
  },
  filters: {
    date (val) {
      let timestamp = Date.parse(val)
      let date = new Date(timestamp)
      let year = date.getFullYear()
      let month = ('0' + (date.getMonth() + 1)).slice(-2)
      let day = ('0' + date.getDate()).slice(-2)
      let hours = date.getHours()
      let minutes = date.getMinutes()

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
    this.getLogs()
  }
}
</script>
