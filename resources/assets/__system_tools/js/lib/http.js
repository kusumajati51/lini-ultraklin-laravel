import Vue from 'vue'
import axios from 'axios'

const http = () => {
  const client = new axios.create({
    baseURL: document.querySelector('meta[name="base-url"').getAttribute('content'),
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })

  return client
}

Object.defineProperties(Vue.prototype, {
  $http: {
    get: http
  }
})
