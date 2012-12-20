require.config
  shim: {
  },
  paths: {
    hm: 'vendor/hm',
    esprima: 'vendor/esprima',
    jquery: 'vendor/jquery.min'
  }

require ['app'], (app) ->
  window.appLoaded = true
  console.log app