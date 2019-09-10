<template>
  <div class="uk-container uk-container-expand">
    <div class="uk-margin-top uk-margin-bottom">
      <div class="uk-card uk-card-default uk-card-small">
        <div class="uk-card-header">
          <h3 class="uk-card-title">Invoice</h3>
        </div>
        <div class="uk-card-body">
          <div uk-grid>
            <div class="uk-width-1-2">
              <div class="uk-margin">
                <label class="uk-form-label">Update Price</label>
                <el-input v-model="input.code" placeholder="Invoice code" clearable>
                  <template slot="append">
                    <el-button @click="updatePrice">Update</el-button>
                  </template>
                </el-input>
              </div>
            </div>
            <div class="uk-width-1-2">
              <div v-if="Object.keys(result).length > 0">
                <h5 class="uk-margin-remove uk-heading-line uk-text-center">
                  <span>Result</span>
                </h5>
                <pre>{{ result }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      input: {
        code: ''
      },
      result: {}
    }
  },
  methods: {
    updatePrice () {
      if (this.input.code.length < 3) return

      this.result.updatePrice = {}

      this.$http.put(`/system-tools/json/invoices/${this.input.code}/update-price`).then(res => {
        this.result = res.data.data
      }).catch(err => {
        if (err.response) {
          let message = err.response.data.message ? err.response.data.message : err.response.statusText

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
