import Vue from 'vue'
import Vuex from 'vuex'

import * as TYPES from './types'

Vue.use(Vuex)

const state = {
  initialized: false,
  config: {},
  regions: []
}

const getters = {
  appInitialized(state) {
    return state.initialized
  }
}

const mutations = {
  [TYPES.INITIALIZE] (state, status) {
    state.initialized = status
  },
  [TYPES.SET_CONFIG](state, config) {
    state.config = config
  },
  [TYPES.SET_REGION](state, regions) {
    state.regions = regions
  }
}

const actions = {
  async getRegions ({ commit }) {
    try {
      let res = await Vue.service.region.all()

      commit(TYPES.SET_REGION, res.data)
    } catch (err) {
      console.log(err.message)
    }
  },
  async initApp ({ commit }) {
    commit(TYPES.INITIALIZE, true)
  }
}

const store = new Vuex.Store({
  state,
  getters,
  mutations,
  actions
})

export default store
