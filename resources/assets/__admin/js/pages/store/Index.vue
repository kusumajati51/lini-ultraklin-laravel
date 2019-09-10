<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header app--card-header-tool">
      <div class="uk-grid-collapse" uk-grid >
        <div class="uk-width-auto">
          <div class="app--card-header-tool-icon app--border-right">
            <i class="fas fa-store fa-lg"></i>
          </div>
        </div>
        <div class="uk-width-expand">
          <h3 class="app--card-title">PARTNERS</h3>
        </div>
        <div class="uk-width-auto uk-hidden">
          <div class="app--card-header-tool-button app--border-left">
            <router-link :to="{ name: 'sales-create' }">
              <i class="fas fa-plus"></i>
            </router-link>
          </div>
        </div>
      </div>
    </div>
    <div class="uk-card-body">
      <div class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
          <thead>
            <tr>
              <th width="20"></th>
              <th>Code</th>
              <th>Name</th>
              <th>Phone</th>
              <th>Owner</th>
              <th class="uk-text-center" width="100">Status</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(store, index) in stores">
              <tr :key="index">
                <td class="uk-text-center">
                    <a href="#" @click.prevent="showDetail(index)">
                        <i v-if="store.collapse" class="fas fa-chevron-right"></i>
                        <i v-else class="fas fa-chevron-down"></i>
                    </a>
                </td>
                <td>{{ store.code }}</td>
                <td>{{ store.name }}</td>
                <td>{{ store.phone }}</td>
                <td>{{ store.owner }}</td>
                <td class="uk-text-center">
                  <el-tag :type="statusTypes[store.status]" size="small" class="uk-text-uppercase">{{ store.status }}</el-tag>
                </td>
              </tr>
              <tr v-if="!store.collapse" :key="`${index}_info`">
                <td colspan="6">
                  <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-3">
                      <div class="uk-margin-small">
                        <div class="app--list-label">Email</div>
                        <div class="app--list-text">{{ store.email }}</div>
                      </div>
                      <div class="uk-margin-small">
                        <div class="app--list-label">Identity Card Number</div>
                        <div class="app--list-text">{{ store.identity_card_number }}</div>
                      </div>
                      <div class="uk-margin-small">
                        <div class="app--list-label">Region</div>
                        <div class="app--list-text">{{ store.region_name }}</div>
                      </div>
                      <div class="uk-margin-small">
                        <div class="app--list-label">Address</div>
                        <div class="app--list-text">{{ store.address }}</div>
                      </div>
                      <div class="uk-margin-small">
                        <div class="app--list-label">Latitude</div>
                        <div class="app--list-text">{{ store.lat }}</div>
                      </div>
                      <div class="uk-margin-small">
                        <div class="app--list-label">Longitude</div>
                        <div class="app--list-text">{{ store.lng }}</div>
                      </div>
                    </div>
                    <div class="uk-width-1-3">
                      <div class="app--list-label">Packages</div>
                      <ul class="uk-list uk-margin-remove">
                        <li v-for="(p, pIndex) in store.packages" :key="pIndex">
                          {{ p.display_name }}
                        </li>
                      </ul>
                    </div>
                    <div class="uk-width-1-3">
                      <div class="uk-margin-small">
                        <div class="app--list-label">Created At</div>
                        <div class="app--list-text">{{ store.created_at }}</div>
                      </div>
                      <div class="uk-margin-small">
                        <div class="app--list-label">Updated At</div>
                        <div class="app--list-text">{{ store.updated_at }}</div>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-auto">
                      <el-button type="small" @click="showPackageDialog(store.id)">Manage Packages</el-button>
                    </div>
                    <div class="uk-width-auto">
                      <el-button type="small" @click="showImageDialog(store.id)">Images</el-button>
                    </div>
                    <div class="uk-width-auto uk-margin-left-auto">
                      <el-button v-if="store.showAcceptButton" type="success" size="small" @click="actionAccept(store.id)">Accept</el-button>
                      <el-button v-if="store.showRejectButton" type="danger" size="small" @click="actionReject(store.id)">Reject</el-button>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
    <div class="uk-card-footer uk-text-center">
      <el-pagination
        :current-page="pagination.current_page"
        :page-size="pagination.per_page"
        :total="pagination.total"
        layout="prev, pager, next">
      </el-pagination>
    </div>

    <el-dialog
      :visible="packageDialog.visible"
      title="Manage Store Packages"
      @close="closePackageDialog">
      <div>
        <div class="uk-margin">
          <el-select v-model="packageDialog.input.packages" class="uk-width-1-1" multiple>
            <el-option
              v-for="item in packages"
              :key="item.id"
              :value="item.id"
              :label="item.display_name">
            </el-option>
          </el-select>
          <p v-if="errors.packages" class="uk-text-small uk-text-danger">{{ errors.packages[0] }}</p>
        </div>
      </div>
      <div slot="footer">
        <el-button type="primary" @click="updatePackages(packageDialog.data.id)">Update</el-button>
      </div>
    </el-dialog>

    <el-dialog
      :visible="imageDialog.visible"
      title="Store Images"
      @close="closeImageDialog">
      <div>
        <div class="uk-grid-small" uk-grid>
          <div v-for="(image, index) in imageDialog.data.images" :key="index" class="uk-width-1-3">
            <div :id="`dialog-image__${index}`">
              <img :src="`${$http.defaults.baseURL}/images/store/1280/${image.filename}`" />
            </div>
          </div>
        </div>
        <hr>
        <div class="uk-text-center">
          <div id="dialog-image__identity-card">
            <img :src="`${$http.defaults.baseURL}/images/store/1280/${imageDialog.data.identity_card}`" />
          </div>
          <h5>{{ imageDialog.data.identity_card_number }}</h5>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import ImageViewer from 'viewerjs'

export default {
  data () {
    return {
      stores: [],
      packages: [],
      pagination: {
        current_page: 1,
        per_page: 24,
        total: 0
      },
      filter: {

      },
      statusTypes: {
        pending: 'warning',
        rejected: 'danger',
        accepted: 'success'
      },
      imageDialog: {
        visible: false,
        data: {}
      },
      packageDialog: {
        visible: false,
        input: {
          packages: ''
        },
        data: {}
      },
      error: false,
      errorMessage: '',
      errors: {}
    }
  },

  created () {
    this.fetchPartners()
  },

  methods: {
    actionAccept (id) {
      const h = this.$createElement

      this.$msgbox({
        title: 'Confimation',
        message: h('div', null, [
          h('span', null, 'Are you sure to '),
          h('span', { style: { color: 'green', 'font-weight': 'bold' } }, 'ACCEPT'),
          h('span', null, ' this store?')
        ]),
        showCancelButton: true
      }).then(() => {
        this.updateStatus(id, 'accepted')
      }).catch(() => {})
    },
    actionReject (id) {
      const h = this.$createElement

      this.$msgbox({
        title: 'Confimation',
        message: h('div', null, [
          h('span', null, 'Are you sure to '),
          h('span', { style: { color: 'red', 'font-weight': 'bold' } }, 'REJECT'),
          h('span', null, ' this store?')
        ]),
        showCancelButton: true
      }).then(() => {
        this.updateStatus(id, 'rejected')
      }).catch(() => {})
    },
    closePackageDialog () {
      this.packageDialog.visible = false
      this.packageDialog.data = {}
    },
    showDetail (index) {
      this.stores[index].collapse = !this.stores[index].collapse
    },
    async showPackageDialog (storeId) {
      let store = this.stores.find(store => store.id === storeId)

      await this.fetchPackages(store.region_id)

      this.packageDialog.visible = true
      this.packageDialog.data = store
      this.packageDialog.input.packages = store.packages.map(p => p.id)
    },
    async showImageDialog (storeId) {
      let store = this.stores.find(store => store.id === storeId)

      this.imageDialog.visible = true
      this.imageDialog.data = store

      setTimeout(() => {
        this.imageDialog.data.images.forEach((item, index) => {
          let imagesEl = document.getElementById(`dialog-image__${index}`)
          let idEl = document.getElementById(`dialog-image__identity-card`)
          let options = {
            movable: false,
            title: false,
            toolbar: false
          }

          new ImageViewer(imagesEl, options)
          new ImageViewer(idEl, options)
        })
      }, 1000)
    },
    closeImageDialog () {
      this.imageDialog.visible = false
    },
    mappingData (store) {
      let isPending = store.status === 'pending'

      store['showAcceptButton'] = isPending
      store['showRejectButton'] = isPending

      return store
    },

    async fetchPackages (regionId) {
      try {
        let res = await this.$service.admin.getPackagesByRegion(regionId)

        this.packages = res.data
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    },
    async fetchPartners () {
      try {
        let res = await this.$service.store.get(this.filter)

        this.stores = res.data.data.map(store => {
          store['collapse'] = true

          return store
        }).map(this.mappingData)
        this.pagination = res.data

        delete this.pagination.data
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    },
    async updatePackages (id) {
      this.error = false
      this.errorMessage = ''
      this.errors = {}

      try {
        let res = await this.$service.store.updatePackages(id, this.packageDialog.input.packages)

        this.stores = this.stores.map(store => {
          if (store.id === res.data.data.id) {
            let $store = res.data.data

            $store['collapse'] = false

            return $store
          }

          return store
        }).map(this.mappingData)

        this.$notify({
          title: 'SUCCESS',
          message: res.data.message,
          type: 'success'
        })

        this.closePackageDialog()
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    },
    async updateStatus (id, val) {
      this.error = false
      this.errorMessage = ''
      this.errors = {}

      try {
        let res = await this.$service.store.updateStatus(id, val)

        this.stores = this.stores.map(store => {
          if (store.id === res.data.data.id) {
            let $store = res.data.data

            $store['collapse'] = false

            return $store
          }

          return store
        }).map(this.mappingData)

        this.$notify({
          title: 'SUCCESS',
          message: res.data.message,
          type: 'success'
        })
      } catch (err) {
        this.$root.notifyErrorHttp(this, err)
      }
    }
  }
}
</script>

<style scoped>
@import url('../../../../../../node_modules/viewerjs/dist/viewer.css');
</style>
