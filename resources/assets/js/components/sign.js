let pass
let env = process.env.NODE_ENV

if (env === 'local' || env === 'development') {
  pass = 'p@ssw0rd'
} else {
  pass = '5y5LZMqv'
}

const sign = {
  id: 'bot32194',
  pass: pass,
  user: {
    merch_id: '32194',
    merch_name: 'Ultraklin'
  }
}

export default sign
