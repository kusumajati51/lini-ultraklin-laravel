<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header"><h3 class="app--card-title">ORDER</h3></div>
    <div class="uk-card-body">
      <div
        :class="`uk-child-width-1-${data.length}`"
        class="uk-grid uk-grid-small"
        uk-grid
      >
        <div>
          <div class="app--box-count uk-text-center">
            <div class="app--box-count-number">{{ counter.today }}</div>
            <div class="app--box-count-text">Today</div>
          </div>
        </div>
        <div>
          <div class="app--box-count uk-text-center">
            <div class="app--box-count-number">{{ counter.yesterday }}</div>
            <div class="app--box-count-text">Yesterday</div>
          </div>
        </div>
        <div>
          <div class="app--box-count uk-text-center">
            <div class="app--box-count-number">{{ counter.lastSevenDays }}</div>
            <div class="app--box-count-text">Last 7 Days</div>
          </div>
        </div>
        <div>
          <div class="app--box-count uk-text-center">
            <div class="app--box-count-number">{{ counter.thisMonth }}</div>
            <div class="app--box-count-text">This Month</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      default: () => ['today', 'yesterday', 'last-7-days', 'this-month'],
      type: Array
    }
  },

  data() {
    return {
      counter: {
        today: 0,
        yesterday: 0,
        lastSevenDays: 0,
        thisMonth: 0
      }
    }
  },

  created() {
    this.fetchData()
  },

  methods: {
    fetchData() {
      this.$http
        .get('/admin/v1/json/widget-resources/order-count')
        .then(res => {
          this.counter = res.data
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    }
  }
}
</script>
