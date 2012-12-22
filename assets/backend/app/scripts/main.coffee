require.config
  deps: ['backbone']
  shim:
    backbone:
      deps: ['lodash', 'jquery']
      exports: 'Backbone'
  paths:
    jquery: 'vendor/jquery.min'
    lodash: 'vendor/lodash.min'
    backbone: 'vendor/backbone-min'
    hm: 'vendor/hm'
    esprima: 'vendor/esprima'
    spec: '/spec'

window.mainLoaded = true

# in test mode mocha will run the app itself
if window.mocha
  return

require ['app'], (app) ->
  console.log 'app loaded'