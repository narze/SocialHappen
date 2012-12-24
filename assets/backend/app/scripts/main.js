(function() {

  require.config({
    deps: ['jquery', 'backbone', 'perfectum_dashboard'],
    shim: {
      backbone: {
        deps: ['lodash', 'jquery'],
        exports: 'Backbone'
      },
      perfectum_dashboard: {
        deps: ['jquery', 'jqueryui', 'bootstrap']
      },
      jqueryPlugins: {
        deps: ['jquery']
      },
      bootstrap: {
        deps: ['jquery']
      },
      sparkline: {
        deps: ['jquery']
      },
      flot: {
        deps: ['jquery']
      },
      flotStack: {
        deps: ['flot']
      },
      flotPie: {
        deps: ['flot']
      },
      flotResize: {
        deps: ['flot']
      },
      jqueryui: {
        deps: ['jquery']
      }
    },
    paths: {
      jquery: 'vendor/jquery.min',
      lodash: 'vendor/lodash.min',
      backbone: 'vendor/backbone-min',
      hm: 'vendor/hm',
      esprima: 'vendor/esprima',
      spec: '/spec',
      text: 'vendor/text',
      bootstrap: 'vendor/jquery-plugins/bootstrap',
      jqueryui: 'vendor/jquery-plugins/jquery-ui-1.8.21.custom.min',
      jqueryPlugins: 'vendor/jquery-plugins',
      sparkline: 'vendor/jquery-plugins/jquery.sparkline',
      flot: 'vendor/jquery-plugins/jquery.flot.min',
      flotStack: 'vendor/jquery-plugins/jquery.flot.stack',
      flotPie: 'vendor/jquery-plugins/jquery.flot.pie.min',
      flotResize: 'vendor/jquery-plugins/jquery.flot.resize.min'
    }
  });

  window.mainLoaded = true;

  window.backend = {
    Models: {},
    Collections: {},
    Views: {},
    Routers: {}
  };

  if (window.mocha) {
    return;
  }

  require(['app'], function(app) {
    return console.log('app loaded');
  });

}).call(this);
