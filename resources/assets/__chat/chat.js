import Vue from 'vue'
import Chat from './index.vue'
import Vuikit from 'vuikit'
import VuikitIcons from '@vuikit/icons'
import UserList from './userList.vue'
import MessagesList from './messages.vue'
import firebase from '../js/firebase'

import '@vuikit/theme'

Vue.use(Vuikit)
Vue.use(VuikitIcons)
Vue.use(firebase)

Vue.component('UserList', UserList)
Vue.component('MessagesList', MessagesList)

new Vue({
  render: r => r(Chat)
}).$mount('#chat-app')
