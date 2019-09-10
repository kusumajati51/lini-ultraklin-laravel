<template>
  <div>
    <div class="uk-margin"><order-count /></div>
    <div class="uk-margin"><order-status-chart /></div>
    <div class="uk-margin"><income-chart /></div>
  </div>
</template>

<script>
import OrderCount from '../widgets/OrderCount'
import OrderStatusChart from '../widgets/OrderStatusChart'
import IncomeChart from '../widgets/IncomeChart'

export default {
  components: {
    OrderCount,
    OrderStatusChart,
    IncomeChart
  }
}
</script>
