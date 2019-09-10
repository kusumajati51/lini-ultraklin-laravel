<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header">
      <div class="uk-grid-small" uk-grid>
        <div class="uk-width-expand">
          <h3 class="app--card-title">INCOME</h3>
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
    <div class="uk-card-body"><canvas id="income-chart" /></div>
    <div class="uk-card-footer">
      <div class="uk-grid-small uk-child-width-1-3" uk-grid>
        <div>
          <div class="app--box-count uk-text-center">
            <div class="app--box-count-number">{{ counter.total }}</div>
            <div class="app--box-count-text">Total</div>
          </div>
        </div>
        <div>
          <div class="app--box-count uk-text-center">
            <div class="app--box-count-number">{{ counter.paid }}</div>
            <div class="app--box-count-text">Paid</div>
          </div>
        </div>
        <div>
          <div class="app--box-count uk-text-center">
            <div class="app--box-count-number">{{ counter.unpaid }}</div>
            <div class="app--box-count-text">Unpaid</div>
          </div>
        </div>
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
      counter: {
        total: 0,
        paid: 0,
        unpaid: 0
      },
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
    createChart(id, data) {
      const ctx = document.getElementById(id).getContext('2d')

      if (this.chart != null) {
        this.chart.destroy()
      }

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: data.datasets
        },
        options: data.options
      })
    },
    fetchData(fn) {
      this.$http
        .get(`/admin/v1/json/widget-resources/daily-income-chart`, {
          params: {
            date: this.filter.date
          }
        })
        .then(({ data }) => {
          this.counter = data.counter

          let labels = data.labels

          let datasets = [
            {
              label: 'PAID',
              backgroundColor: 'rgba(77,182,172 ,0.5)',
              borderColor: 'rgba(77,182,172 ,1)',
              data: data.data.paid,
              fill: false
            },
            {
              label: 'UNPAID',
              backgroundColor: 'rgba(229,115,115 ,0.5)',
              borderColor: 'rgba(229,115,115 ,1)',
              data: data.data.unpaid,
              hidden: true,
              fill: false
            }
          ]

          let options = {
            tooltips: {
              callbacks: {
                label: function(tooltipItem, data) {
                  return tooltipItem.yLabel
                    .toString()
                    .replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                }
              }
            },
            scales: {
              yAxes: [
                {
                  ticks: {
                    callback: function(value, index, values) {
                      return value
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                    }
                  }
                }
              ]
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
    refreshChart() {
      this.fetchData(data => {
        this.createChart('income-chart', data)
      })
    }
  }
}
</script>
