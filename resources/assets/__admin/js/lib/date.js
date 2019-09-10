import Vue from 'vue'

const date = () => {
  let dateObject = {}

  let __date = new Date()
  let year = __date.getFullYear()
  let month = __date.getMonth()
  let date = __date.getDate()

  dateObject.today = () => {
    let y = year
    let m = ('0' + (month + 1)).toString().slice(-2)
    let d = ('0' + date).toString().slice(-2)

    return `${y}-${m}-${d}`
  }

  dateObject.startOfMonth = () => {
    let y = year
    let m = ('0' + (month + 1)).toString().slice(-2)
    let d = 1

    return `${y}-${m}-${d}`
  }

  dateObject.endOfMonth = () => {
    let y = year
    let m = ('0' + (month + 1)).toString().slice(-2)
    let d = new Date(year, month + 1, 0).getDate()

    return `${y}-${m}-${d}`
  }

  return dateObject
}

Object.defineProperties(Vue.prototype, {
  $date: {
    get() {
      return date
    }
  }
})
