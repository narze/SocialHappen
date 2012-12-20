(function() {

  require.config({
    shim: {},
    paths: {
      hm: 'vendor/hm',
      esprima: 'vendor/esprima',
      jquery: 'vendor/jquery.min'
    }
  });

  require(['app'], function(app) {
    window.appLoaded = true;
    return console.log(app);
  });

}).call(this);
