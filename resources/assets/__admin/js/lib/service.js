import Vue from 'vue'

let admin = {
  getPackages () {
    return Vue.http().get('/admin/v1/json/u/packages')
  },
  getPackagesByRegion (regionId) {
    return Vue.http().get(`/admin/v1/json/u/regions/${regionId}/packages`)
  },
  getRegions () {
    return Vue.http().get('/admin/v1/json/u/regions')
  }
}

let order = {
  export (params = {}) {
    return Vue.http().get('/admin/v1/json/orders', {
      params: params,
      responseType: 'blob'
    })
  },
  find (code) {
    return Vue.http().get(`/admin/v1/json/orders/${code}`)
  },
  findByUpline (userCode, orderCode) {
    return Vue.http().get(`/admin/v1/json/sales/${userCode}/downline/orders/${orderCode}`)
  },
  get (params = {}) {
    return Vue.http().get('/admin/v1/json/orders', {
      params: params
    })
  },
  updateStatus (code, status) {
    return Vue.http().put(`/admin/v1/json/orders/${code}/status`, {
      status: status
    })
  }
}

let region = {
  all () {
    return Vue.http().get('/admin/v1/json/u/regions')
  }
}

let store = {
  get (params = {}) {
    return Vue.http().get('/admin/v1/json/stores', {
      params: params
    })
  },
  updateStatus (id, val) {
    return Vue.http().put(`/admin/v1/json/stores/${id}/status`, {
      status: val
    })
  },
  updatePackages (id, packages) {
    return Vue.http().put(`/admin/v1/json/stores/${id}/packages`, {
      packages: packages
    })
  }
}

Vue.service = {
  admin,
  order,
  region,
  store,
  getAgents (params = {}) {
    return Vue.http().get('/admin/v1/json/agents', {
      params: params
    })
  },
  getSales (params = {}) {
    return Vue.http().get('/admin/v1/json/sales', {
      params: params
    })
  }
}

Object.defineProperties(Vue.prototype, {
  $service: {
    get: () => { return Vue.service }
  }
})
