import firebase from 'firebase/app'
import 'firebase/messaging'
import 'firebase/database'
require('./bootstrap')

let env = process.env.NODE_ENV

let adminURL

if (env === 'local') {
  adminURL = 'http://localhost:8888/admin'
} else if (env === 'development') {
  adminURL = 'https://dev-api.ultraklin.com/admin'
} else if (env === 'production') {
  adminURL = 'https://api.ultraklin.com/admin'
}

// const adminURL = 'http://dev-web.ultraklin.com/api/lini-ultraklin-laravel/public/admin'
// const adminURL = 'https://api.ultraklin.com/admin'
// const adminURL = 'http://localhost:8888/admin'
// const adminURL = 'https://dev-api.ultraklin.com/admin'

var config = {
  apiKey: 'AIzaSyAbwMdmrnU98nxASSqZfEyly9FGlO4BkAs',
  authDomain: 'ultraklin-f95b2.firebaseapp.com',
  databaseURL: 'https://ultraklin-f95b2.firebaseio.com',
  projectId: 'ultraklin-f95b2',
  storageBucket: 'ultraklin-f95b2.appspot.com',
  messagingSenderId: '1067928137424'
}
firebase.initializeApp(config)

const messaging = firebase.messaging()

messaging.usePublicVapidKey(
  'BN3KKw1M8NXlCBKsmSQ7mK3j-MLI1cuTdUtnoDB4P3FfLaggu06OYZ99geE1ZXMXQXInjsKHMG3j4Tu2D-dv8zM'
)

function reqPermission () {
  messaging
    .requestPermission()
    .then(function () {
      console.log('Notification permission granted.')
      // TODO(developer): Retrieve an Instance ID token for use with FCM.
      // ...
    })
    .catch(function (err) {
      console.log('Unable to get permission to notify.', err)
    })
}

function sendTokenToServer (token) {
  // var config = {
  //   headers: {
  //     // 'Content-Type': 'application/json'
  //     // 'Accept': 'application/json'
  //     // 'Authorization': document.cookie
  //   }
  //   // withCredentials: true
  // }
  let body = {
    token: token,
    type: 'CMS'
  }
  window.axios.post(adminURL + '/fcmTokenOfficer', body).then(res => {
    console.log(res.data)
  })
}

// var sw = require('./firebase-messaging-sw')
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    console.log(env)
    navigator.serviceWorker
      .register('/service-worker.js')
      .then(registration => {
        messaging.useServiceWorker(registration)

        reqPermission()

        messaging
          .getToken()
          .then(function (currentToken) {
            if (currentToken) {
              sendTokenToServer(currentToken)
              // updateUIForPushEnabled(currentToken)
              console.log(currentToken)
            } else {
              // Show permission request.
              console.log(
                'No Instance ID token available. Request permission to generate one.'
              )
              // Show permission UI.
              // updateUIForPushPermissionRequired()
              // setTokenSentToServer(false)
              reqPermission()
            }
          })
          .catch(function (err) {
            console.log('An error occurred while retrieving token. ', err)
            // showToken('Error retrieving Instance ID token. ', err)
            // setTokenSentToServer(false)
          })

        messaging.onMessage(function (payload) {
          console.log('Message received. ', payload)
          var notificationTitle = payload.notification.title
          var notificationOptions = {
            body: payload.notification.body,
            icon: '/firebase-logo.png'
          }

          return registration.showNotification(
            notificationTitle,
            notificationOptions
          )
        })
      })
  })
}
