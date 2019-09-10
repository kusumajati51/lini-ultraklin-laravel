let mix = require('laravel-mix')
const workboxPlugin = require('workbox-webpack-plugin')
const { VueLoaderPlugin } = require('vue-loader')

// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

mix.webpackConfig({
  module: {
    rules: [
      {
        enforce: 'pre',
        test: /\.(js|vue)$/,
        loader: 'eslint-loader',
        exclude: /node_modules/
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              [
                'env',
                {
                  modules: false
                }
              ]
            ],
            plugins: ['transform-runtime', 'transform-object-rest-spread']
          }
        }
      }
    ]
  },
  plugins: [
    new VueLoaderPlugin()
  ]
})

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

if (process.env.RES_ENV && process.env.RES_ENV == 'admin') {
  mix
    .js('resources/assets/__admin/js/app.js', 'public/js/admin.js')
    .sass('resources/assets/__admin/sass/app.scss', 'public/css/admin.css')
} else if (process.env.RES_ENV && process.env.RES_ENV == 'system-tools') {
  mix
    .js(
      'resources/assets/__system_tools/js/app.js',
      'public/js/system-tools.js'
    )
    .sass(
      'resources/assets/__system_tools/sass/app.scss',
      'public/css/system-tools.css'
    )
} else {
  mix
    .js('resources/assets/js/main.js', 'public/js')
    .js('resources/assets/__chat//chat.js', 'public/js')
    .js('resources/assets/js/firebase.js', 'public/js')
    .extract(['vue'])
    .sourceMaps()
    .sass('resources/assets/sass/app.scss', 'public/css')
    .webpackConfig({
      output: {
        publicPath: './'
      },
      node: {
        fs: 'empty',
        module: 'empty'
      },
      plugins: [
        new workboxPlugin.InjectManifest({
          swSrc: './resources/assets/js/firebase-messaging-sw.js'
        }),
        new workboxPlugin.GenerateSW({
          swDest: path.join(`${__dirname}/public`, 'service-worker.js'),
          clientsClaim: true,
          skipWaiting: true,
          importScripts: '/firebase-messaging-sw.js'
        })
      ]
    })
}
