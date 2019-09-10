export default {
  methods: {
    notifyErrorHttp (ctx, err) {
      if (err.response) {
        let message = err.response.data.message
          ? err.response.data.message
          : err.response.statusText

        this.$notify({
          title: 'ERROR',
          message: message,
          type: 'error'
        })

        let keys = Object.keys(ctx.$options.data())

        if (keys.includes('error')) {
          ctx.error = true
        }

        if (keys.includes('errorMessage')) {
          ctx.errorMessage = message
        }

        if (keys.includes('errors') && err.response.data.error_validation) {
          ctx.errors = err.response.data.errors
        }
      } else {
        console.error(err.message)
      }
    }
  }
}
