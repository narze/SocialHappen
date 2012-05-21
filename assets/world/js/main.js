// Require.js allows us to configure shortcut alias
// Their usage will become more apparent futher along in the tutorial.
require.config({
  paths: {
    // Major libraries
    jquery: 'libs/jquery/jquery-min',
    underscore: 'libs/underscore/underscore-min', // https://github.com/amdjs
    backbone: 'libs/backbone/backbone-min', // https://github.com/amdjs
    bootstrap: 'libs/bootstrap/bootstrap.min',
    timeago: 'libs/jquery.timeago/jquery.timeago',
    masonry: 'libs/masonry/jquery.masonry.min',
    endlessscroll: 'libs/jquery.endless-scroll/jquery.endless-scroll.min', // https://github.com/paulirish/infinite-scroll/
    // Require.js plugins
    text: 'libs/require/text',
    order: 'libs/require/order',

    // Just a short cut so we can put our html outside the js dir
    // When you have HTML/CSS designers this aids in keeping them out of the js directory
    templates: '../templates'
  },
	urlArgs: 'bust=' + (new Date()).getTime()

});

// Let's kick off the application

require([
  'views/app',
  'router',
  'vm',
  'models/user',
  'collections/challenges',
  'events'
], function(AppView, Router, Vm, UserModel, ChallengesCollection, vent){
  
  var currentUserModel = window.World.currentUserModel = new UserModel();
  var challengesCollection = window.World.challengesCollection = new ChallengesCollection([]);
  
  var appView = Vm.create({}, 'AppView', AppView, {
    currentUserModel: currentUserModel,
    vent: vent
  });
  appView.render();
  
  // The router now has a copy of all main appview
  Router.initialize({
    appView: appView,
    currentUserModel: currentUserModel,
    challengesCollection: challengesCollection,
    vent: vent
  });
  
});