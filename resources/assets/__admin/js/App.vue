<template>
  <div class="uk-container uk-margin-top uk-margin-bottom">
    <div class="uk-grid-small" uk-grid>
      <div class="uk-width-medium"><sidebar :menu="menu" /></div>
      <div class="uk-width-expand"><router-view /></div>
    </div>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar'

export default {
  components: {
    Sidebar
  },

  data() {
    return {
      menu: ''
    }
  },

  created() {
    this.fetchMenu()
  },

  methods: {
    fetchMenu() {
      this.$http
        .get('/admin/v1/json/menu')
        .then(res => {
          this.menu = res.data
        })
        .catch(err => {
          if (err.response) {
            let message = err.response.data.message
              ? err.response.data.message
              : err.response.statusText

            this.$notify({
              title: 'ERROR',
              message: message,
              type: 'error'
            })
          }
        })
    }
  }
}
</script>
