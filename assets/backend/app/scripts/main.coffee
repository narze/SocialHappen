require.config
  deps: ['jquery', 'backbone', 'perfectum_dashboard']
  shim:
    backbone:
      deps: ['lodash', 'jquery']
      exports: ->
        _.templateSettings =
          evaluate: /<%([\s\S]+?)%>/g
          interpolate : /\{\{([\s\S]+?)\}\}/g
          escape : /\{\{\{([\s\S]+?)\}\}\}/g
        window.Backbone
    perfectum_dashboard:
      deps: [
        'jquery'
        'jqueryui'
        'bootstrap'
      ]
    jqueryPlugins:
      deps: ['jquery']
    bootstrap:
      deps: ['jquery']
    sparkline:
      deps: ['jquery']
    flot:
      deps: ['jquery']
    flotStack:
      deps: ['flot']
    flotPie:
      deps: ['flot']
    flotResize:
      deps: ['flot']
    jqueryui:
      deps: ['jquery']
    moment:
      exports: 'moment'
    backbonePaginator:
      deps: ['backbone']
    backboneValidation:
      deps: ['backbone']
    backboneValidationBootstrap:
      deps: ['backboneValidation']
    jqueryForm:
      deps: ['jquery']

  paths:
    jquery: 'vendor/jquery.min'
    lodash: 'vendor/lodash.min'
    backbone: 'vendor/backbone-min'
    hm: 'vendor/hm'
    esprima: 'vendor/esprima'
    spec: '/spec'
    text: 'vendor/text'
    moment: 'vendor/moment.min'
    backbonePaginator: 'vendor/backbone.paginator'
    backboneValidation: 'vendor/backbone-validation-amd'
    backboneValidationBootstrap: 'vendor/backbone-validation-bootstrap'
    jqueryForm: 'vendor/jquery-plugins/jquery.form'

    # Perfectum Dashboard
    bootstrap: 'vendor/jquery-plugins/bootstrap'
    jqueryui: 'vendor/jquery-plugins/jquery-ui-1.8.21.custom.min'
    jqueryPlugins: 'vendor/jquery-plugins'
    sparkline: 'vendor/jquery-plugins/jquery.sparkline'
    flot: 'vendor/jquery-plugins/jquery.flot.min'
    flotStack: 'vendor/jquery-plugins/jquery.flot.stack'
    flotPie: 'vendor/jquery-plugins/jquery.flot.pie.min'
    flotResize: 'vendor/jquery-plugins/jquery.flot.resize.min'

window.mainLoaded = true

window.backend =
  Models: {}
  Collections: {}
  Views: {}
  Routers: {}
