// Require.js allows us to configure shortcut alias
// Their usage will become more apparent futher along in the tutorial.
require.config({
  paths: {
    // Major libraries
    jquery: '../../libs/jquery/jquery-min',
    underscore: '../../libs/underscore/underscore-min', // https://github.com/amdjs
    backbone: '../../libs/backbone/backbone-min', // https://github.com/amdjs
    bootstrap: '../../libs/bootstrap/bootstrap.min',
    timeago: '../../libs/jquery.timeago/jquery.timeago',
    masonry: '../../libs/masonry/jquery.masonry.min',
    moment: '../../libs/moment/moment.min',
    sandbox: '../../libs/sandbox/sandbox',
    // Require.js plugins
    text: '../../libs/require/text',
    order: '../../libs/require/order',

    // Just a short cut so we can put our html outside the js dir
    // When you have HTML/CSS designers this aids in keeping them out of the js directory
    templates: '../templates'
  },
	urlArgs: 'bust=' +  (new Date()).getTime()

});

// Let's kick off the application

require([
  'views/app',
  'router',
  'vm',
  'models/user',
  'collections/activitys',
  'collections/achievements',
  'collections/coupons',
  'collections/actions',
  'collections/cards',
  'events',
  'sandbox'
], function(AppView, Router, Vm, UserModel, ActivityCollection, AchievementCollection, CouponCollection, ActionCollection, CardCollection, vent, sandbox){

  var userModel = sandbox.models.userModel = window.Passport.userModel = new UserModel();
  var currentUserModel = sandbox.models.currentUserModel = window.Passport.currentUserModel = new UserModel();
  var activityCollection = sandbox.collections.activityCollection = window.Passport.activityCollection = new ActivityCollection([]);
  var actionCollection = sandbox.collections.actionCollection = window.Passport.actionCollection = new ActionCollection([]);
  var achievementCollection = sandbox.collections.achievementCollection = window.Passport.achievementCollection = new AchievementCollection([]);
  var couponCollection = sandbox.collections.couponCollection = window.Passport.couponCollection = new CouponCollection([]);
  var cardCollection = sandbox.collections.cardCollection = window.Passport.cardCollection = new CardCollection([]);

  var appView = Vm.create({}, 'AppView', AppView, {

  });
  appView.render();

  // The router now has a copy of all main appview
  Router.initialize({
    appView: appView
  });

});
