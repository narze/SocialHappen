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
      esprima: 'vendor/esprima',
      spec: '/spec'
    }
  });

  require(['app'], function(app) {
    console.log('App loaded');
    return require(['spec/app-test', 'spec/collections/company-collection-test'], function() {
      return require(['../runner/mocha']);
    });
  });

  window.mainLoaded = true;

}).call(this);
