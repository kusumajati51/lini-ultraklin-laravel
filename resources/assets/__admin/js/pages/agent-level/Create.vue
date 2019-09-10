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
          <h3 class="app--card-title">{{ title }}</h3>
        </div>
      </div>
    </div>
    <div class="uk-card-body">
      <div class="uk-margin uk-position uk-position-relative">
        <label class="uk-form-label">Name</label>
        <el-input v-model="input.name"></el-input>
        <div v-if="errors.name" class="el-form-item__error">
          {{ errors.name[0] }}
        </div>
      </div>
      <div class="uk-margin">
        <label class="uk-form-label">Description</label>
        <el-input
          v-model="input.description"
          type="textarea"
          rows="5"
        ></el-input>
      </div>
      <div
        v-for="(service, index) in input.services"
        :key="index"
        class="uk-margin uk-position uk-position-relative"
      >
        <label class="uk-form-label">{{ service.display_name }}</label>
        <el-input v-model="service.value" type="number" min="0">
          <template slot="prepend">
            <el-checkbox
              v-model="service.percent"
              label="Percent"
            ></el-checkbox>
          </template>
          <template slot="append">
            <span v-if="service.percent">%</span> <span v-else>IDR</span>
          </template>
        </el-input>
        <div
          v-if="errors[`services.${index}.value`]"
          class="el-form-item__error"
        >
          {{ errors[`services.${index}.value`][0] }}
        </div>
      </div>
    </div>
    <div class="uk-card-footer uk-text-right">
      <el-button type="primary" @click="save">SAVE</el-button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      edit: false,
      title: 'NEW AGENT LEVEL',
      input: {
        name: '',
        description: '',
        services: []
      },
      error: false,
      errorMessage: '',
      errors: []
    }
  },

  created() {
    this.fetchServices()

    if (this.$route.params.id) {
      this.edit = true
      this.title = 'EDIT AGENT LEVEL'

      this.getLevel()
    }
  },

  methods: {
    fetchServices() {
      this.$http
        .get('/admin/v1/json/services/list')
        .then(res => {
          this.input.services = res.data.map(service => {
            let item = {
              id: service.id,
              name: service.name,
              display_name: service.display_name,
              percent: true,
              value: 0
            }

            return item
          })
        })
        .catch(err => {
          if (err.response) {
            this.error = true
            this.errorMessage = err.response.data.message
              ? err.response.data.message
              : err.response.statusText

            this.$notify({
              title: 'ERROR',
              message: this.errorMessage,
              type: 'error'
            })
          }
        })
    },
    getLevel() {
      this.$http
        .get(`/admin/v1/json/agent-levels/${this.$route.params.id}/edit`)
        .then(res => {
          this.input.name = res.data.name
          this.input.description = res.data.description
          res.data.services.forEach(service => {
            let index = this.input.services.findIndex(item => {
              return service.id === item.id
            })

            this.input.services[index].id = service.id
            this.input.services[index].name = service.name
            this.input.services[index].display_name = service.display_name
            this.input.services[index].percent = service.pivot.percent === 1
            this.input.services[index].value = service.pivot.value
          })
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)

          this.$router.push({
            name: 'agent-level'
          })
        })
    },
    save() {
      if (this.edit) {
        this.update()
      } else {
        this.store()
      }
    },
    store() {
      this.error = false
      this.errorMessage = ''
      this.errors = ''

      this.$http
        .post('/admin/v1/json/agent-levels', this.input)
        .then(res => {
          if (res.data.success) {
            this.$notify({
              title: 'SUCCESS',
              message: res.data.message,
              type: 'success'
            })

            this.$router.push({
              name: 'agent-level'
            })
          }
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    update() {
      this.error = false
      this.errorMessage = ''
      this.errors = {}

      this.$http
        .put(`/admin/v1/json/agent-levels/${this.$route.params.id}`, this.input)
        .then(res => {
          if (res.data.success) {
            this.$notify({
              title: 'SUCCESS',
              message: res.data.message,
              type: 'success'
            })

            this.$router.push({
              name: 'agent-level'
            })
          }
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    }
  }
}
</script>
