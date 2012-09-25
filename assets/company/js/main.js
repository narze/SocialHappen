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
    jqueryui: '../../libs/jquery.ui/jquery-ui-1.8.20.custom.min',
    endlessscroll: '../../libs/jquery.endless-scroll/jquery.endless-scroll.min', // https://github.com/paulirish/infinite-scroll/
    moment: '../../libs/moment/moment.min',
    jqueryForm: '../../libs/jquery.form/jquery.form',
    sandbox: '../../libs/sandbox/sandbox',
    // Require.js plugins
    text: '../../libs/require/text',
    order: '../../libs/require/order',

    // Just a short cut so we can put our html outside the js dir
    // When you have HTML/CSS designers this aids in keeping them out of the js directory
    templates: '../templates'
  },
	// urlArgs: 'bust=' + (new Date()).getTime()
  shim: {
    // Backbone library depends on lodash and jQuery.
    underscore: {
        deps: ['jquery'],
        exports: '_'
    },
    backbone: {
        deps: ['underscore', 'jquery'],
        exports: 'Backbone'
    },

    //jQuery plugins
    timeago: ['jquery'],
    jqueryForm: ['jquery'],
    bootstrap: ['jquery'],
    masonry: ['jquery'],
    endlessscroll: ['jquery']
  }

});

// Let's kick off the application

require([
  'views/app',
  'router',
  'vm',
  'models/user',
  'models/challenger',
  'models/company',
  'collections/challenges',
  'collections/rewards',
  'collections/coupons',
  'collections/activities',
  'collections/company-users',
  'events',
  'sandbox'
], function(AppView, Router, Vm, UserModel, ChallengerModel, CompanyModel, ChallengesCollection, RewardsCollection, CouponsCollection, ActivitiesCollection, CompanyUsersCollection, vent, sandbox){

  sandbox.models.currentUserModel = new UserModel();
  sandbox.models.challengerModel = new ChallengerModel();
  sandbox.models.companyModel= new CompanyModel();
  sandbox.collections.challengesCollection = new ChallengesCollection([]);
  sandbox.collections.rewardsCollection = new RewardsCollection([]);
  sandbox.collections.couponsCollection = new CouponsCollection([]);
  sandbox.collections.activitiesCollection = new ActivitiesCollection([]);
  sandbox.collections.companyUsersCollection = new CompanyUsersCollection([]);

  sandbox.models.currentUserModel.fetch({
    success: function(model, xhr){
      if(xhr.user_id){
        return initView();
      }

      window.location = window.Company.BASE_URL + '/login?next=' + window.location.href
    }
  });

  function initView() {
    var appView = Vm.create({}, 'AppView', AppView, {});
    appView.render();

    // The router now has a copy of all main appview
    Router.initialize({
      appView: appView
    });
  }
});
