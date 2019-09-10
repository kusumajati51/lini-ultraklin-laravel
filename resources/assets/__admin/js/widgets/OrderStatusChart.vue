<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header">
      <div class="uk-grid-small" uk-grid>
        <div class="uk-width-expand">
          <h3 class="app--card-title">ORDER STATUS</h3>
        </div>
        <div class="uk-with-auto">
          <el-date-picker
            v-model="filter.date"
            format="yyyy-MM"
            value-format="yyyy-MM"
            type="month"
            size="small"
            @change="refreshChart"
          />
        </div>
      </div>
    </div>
    <div class="uk-card-body">
      <div class="uk-grid-small" uk-grid>
        <div class="uk-width-1-2"><canvas id="order-status-chart" /></div>
        <div class="uk-width-1-2"><div id="order-status-chart-legend" /></div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js'

export default {
  data() {
    return {
      chart: null,
      filter: {
        date: `${new Date().getFullYear()}-${(
          '0' +
          (new Date().getMonth() + 1)
        ).slice(-2)}`
      }
    }
  },

  mounted() {
    this.refreshChart()
  },

  methods: {
    fetchData(fn) {
      this.$http
        .get('/admin/v1/json/widget-resources/order-status-chart', {
          params: {
            date: this.filter.date
          }
        })
        .then(res => {
          let labels = res.data.labels

          let datasets = [
            {
              data: res.data.data,
              backgroundColor: res.data.backgroundColor
            }
          ]

          let options = {
            legend: false,
            legendCallback: chart => {
              let labels = chart.data.labels
              let dataset = chart.data.datasets[0]
              let totalOrder = dataset.data.reduce((total, val) => total + val)

              let html = []

              for (let i = 0; i < dataset.data.length; i++) {
                let percent = Math.round((dataset.data[i] / totalOrder) * 100)

                html.push(`<div class="uk-margin-small">`)
                html.push(
                  `<div style="font-size: 14px">${labels[i]} <small>(${
                    dataset.data[i]
                  })</small></div>`
                )
                html.push(
                  `<div role="progressbar" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100" class="el-progress el-progress--line">`
                )
                html.push(`<div class="el-progress-bar">`)
                html.push(`<div class="el-progress-bar__outer">`)
                html.push(
                  `<div class="el-progress-bar__inner" style="width: ${percent}%; background-color: ${
                    dataset.backgroundColor[i]
                  };">`
                )
                html.push(`</div></div></div>`)
                html.push(`<div class="el-progress__text">${percent}%</div>`)
                html.push(`</div></div>`)
              }

              return html.join('')
            }
          }

          return fn({
            labels,
            datasets,
            options
          })
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    createChart(id, data) {
      let ctx = document.getElementById(id).getContext('2d')
      let legend = document.getElementById(`${id}-legend`)

      if (this.chart != null) {
        this.chart.destroy()
        legend.innerHTML = ''
      }

      this.chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: data.labels,
          datasets: data.datasets
        },
        options: data.options
      })

      legend.innerHTML = this.chart.generateLegend()
    },
    refreshChart() {
      this.fetchData(data => {
        this.createChart('order-status-chart', data)
      })
    }
  }
}
</script>
