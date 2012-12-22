(function() {

  require.config({
    baseUrl: 'scripts',
    shim: {
      backbone: {
        deps: ['lodash', 'jquery'],
        exports: 'Backbone'
      }
    },
    paths: {
      jquery: 'vendor/jquery.min',
      lodash: 'vendor/lodash.min',
      backbone: 'vendor/backbone-min',
      hm: 'vendor/hm',
      esprima: 'vendor/esprima'
    }
  });

  require(['app'], function(app) {
    return console.log('App loaded');
  });

  window.mainLoaded = true;

}).call(this);
