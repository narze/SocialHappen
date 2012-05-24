// Require.js allows us to configure shortcut alias
// Their usage will become more apparent futher along in the tutorial.
require.config({
  paths: {
    // Major libraries
    jquery: 'libs/jquery/jquery-min',
    underscore: 'libs/underscore/underscore-min', // https://github.com/amdjs
    backbone: 'libs/backbone/backbone-min', // https://github.com/amdjs
    sinon: 'libs/sinon/sinon.js',
    bootstrap: 'libs/bootstrap/bootstrap.min',
    timeago: 'libs/jquery.timeago/jquery.timeago',
    masonry: 'libs/masonry/jquery.masonry.min',
    endlessscroll: 'libs/jquery.endless-scroll/jquery.endless-scroll.min', // https://github.com/paulirish/infinite-scroll/
    
    //SocialHappen libs
    shplainbar: 'libs/sh/plain-bar',

    // Require.js plugins
    text: 'libs/require/text',
    order: 'libs/require/order',

    // Just a short cut so we can put our html outside the js dir
    // When you have HTML/CSS designers this aids in keeping them out of the js directory
    templates: '../templates'
  },
  urlArgs: "bust=" +  (new Date()).getTime()

});

// Let's kick off the application

require([
  'views/settings/challenge',
  'router',
  'vm',
  'events',
  'shplainbar'
], function(AppView, Router, Vm, vent, Shplainbar){
  
  var appView = Vm.create({
    vent: vent
  }, 'AppView', AppView);
  appView.render();
  
  // The router now has a copy of all main appview
  Router.initialize({
    appView: appView,
    vent: vent
  });
});
