<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid>
        <div class="uk-width-auto">
          <div class="app--card-header-tool-button app--border-right">
            <router-link :to="{ name: 'agent' }">
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
      <div uk-grid>
        <div class="uk-width-1-2">
          <label class="uk-form-label">User</label>
          <div class="uk-margin">
            <el-autocomplete
              v-model="input.userString"
              class="inline-input uk-width-1-1"
              placeholder="Please input name / email / phone"
              :fetch-suggestions="queryUser"
              :trigger-on-focus="false"
              :disabled="this.edit"
              @select="onSelectUser"
            >
              <template slot-scope="{ item }">
                <div class="uk-text-uppercase uk-text-bold">
                  {{ item.value }}
                </div>
                <div>{{ item.data.email }}</div>
                <div>{{ item.data.phone }}</div>
              </template>
            </el-autocomplete>
            <p
              v-if="errors.id"
              class="uk-margin-small uk-text-danger uk-text-small"
            >
              {{ errors.id[0] }}
            </p>
          </div>
          <div class="uk-margin">
            <label class="uk-form-label">Level</label>
            <el-select v-model="input.level" class="uk-width-1-1">
              <el-option
                v-for="item in options.level"
                :key="item.value"
                :value="item.value"
                :label="item.label"
              ></el-option>
            </el-select>
            <p
              v-if="errors.level"
              class="uk-margin-small uk-text-danger uk-text-small"
            >
              {{ errors.level[0] }}
            </p>
          </div>
        </div>
        <div class="uk-width-1-2">
          <ul class="uk-list">
            <li>
              <span class="app--list-label">Name</span>
              <span class="app--list-text">{{ input.user.name }}</span>
            </li>
            <li>
              <span class="app--list-label">Email</span>
              <span class="app--list-text">{{ input.user.email }}</span>
            </li>
            <li>
              <span class="app--list-label">Phone</span>
              <span class="app--list-text">{{ input.user.phone }}</span>
            </li>
          </ul>
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
      title: 'NEW AGENT',
      input: {
        userString: '',
        user: {
          id: '',
          name: '-',
          email: '-',
          phone: '-'
        },
        level: ''
      },
      options: {
        level: []
      },
      resources: {
        user: []
      },
      error: false,
      errorMessage: '',
      errors: {}
    }
  },

  created() {
    this.levels()

    if (this.$route.params.id) {
      this.edit = true
      this.title = 'EDIT AGENT'

      this.getAgent()
    }
  },

  methods: {
    levels() {
      this.$http
        .get('/admin/v1/json/agent-levels/list')
        .then(res => {
          this.options.level = res.data.map(item => {
            return {
              value: item.id,
              label: item.name
            }
          })
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    getAgent() {
      this.error = false
      this.errorMessage = ''

      this.$http
        .get(`/admin/v1/json/agents/${this.$route.params.id}/edit`)
        .then(res => {
          this.input.userString = res.data.name
          this.input.user.name = res.data.name
          this.input.user.email = res.data.email
          this.input.user.phone = res.data.phone
          this.input.level = res.data.level.id
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)

          this.$router.push({
            name: 'agent'
          })
        })
    },
    queryUser(queryString, cb) {
      this.$http
        .get(`/admin/json/users`, {
          params: {
            search: this.input.userString,
            sort: ['name', 'asc'],
            status: ['user', 'tester']
          }
        })
        .then(response => {
          this.resources.user = response.data.data.map(item => {
            return {
              value: item.name,
              data: item
            }
          })

          cb(this.resources.user)
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    onSelectUser(item) {
      this.input.user = item.data
    },
    save() {
      if (this.edit) {
        this.update()
      } else {
        this.store()
      }
    },
    store() {
      this.error = ''
      this.errorMessage = ''
      this.errors = {}

      this.$http
        .post('/admin/v1/json/agents', {
          id: this.input.user.id,
          level: this.input.level
        })
        .then(res => {
          this.$notify({
            title: 'SUCCESS',
            message: res.data.message,
            type: 'success'
          })

          this.$router.push({
            name: 'agent'
          })
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    },
    update() {
      this.error = ''
      this.errorMessage = ''
      this.errors = {}

      this.$http
        .put(`/admin/v1/json/agents/${this.$route.params.id}`, {
          level: this.input.level
        })
        .then(res => {
          this.$notify({
            title: 'SUCCESS',
            message: res.data.message,
            type: 'success'
          })

          this.$router.push({
            name: 'agent'
          })
        })
        .catch(err => {
          this.$root.notifyErrorHttp(this, err)
        })
    }
  }
}
</script>
