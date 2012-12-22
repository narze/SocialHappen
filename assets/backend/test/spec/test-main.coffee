require.config
  baseUrl: 'scripts'
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

require ['app'], (app) ->
  console.log 'App loaded'
  require [
    # //list tests
    'spec/app-test',
    'spec/collections/company-collection-test'
  ], ->
    require(['../runner/mocha']) #run mocha

window.mainLoaded = true