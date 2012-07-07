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
    jqueryui: 'libs/jquery.ui/jquery-ui-1.8.20.custom.min',
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
  'models/company',
  'collections/challenges',
  'collections/rewards',
  'collections/coupons',
  'events'
], function(AppView, Router, Vm, UserModel, CompanyModel, ChallengesCollection, RewardsCollection, CouponsCollection, vent){
  
  var currentUserModel = window.Company.currentUserModel = new UserModel();
  var currentCompanyModel = window.Company.currentCompanyModel = new CompanyModel();
  var challengesCollection = window.Company.challengesCollection = new ChallengesCollection([]);
  var rewardsCollection = window.Company.rewardsCollection = new RewardsCollection([]);
  var couponsCollection = window.Company.couponsCollection = new CouponsCollection([]);
  
  var appView = Vm.create({}, 'AppView', AppView, {
    currentUserModel: currentUserModel,
    vent: vent
  });
  appView.render();
  
  // The router now has a copy of all main appview
  Router.initialize({
    appView: appView,
    currentUserModel: currentUserModel,
    currentCompanyModel: currentCompanyModel,
    challengesCollection: challengesCollection,
    rewardsCollection: rewardsCollection,
    couponsCollection: couponsCollection,
    vent: vent
  });
  
});
