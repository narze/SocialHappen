// Filename: router.js
define([
  'jquery',
  'underscore',
  'backbone',
	'vm'
], function ($, _, Backbone, Vm) {
  var AppRouter = Backbone.Router.extend({
    routes: {
      // Pages
      '/profile/:id': 'profile',
    
      // Default - catch all
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
		var appView = options.appView;
    var router = new AppRouter(options);
		router.on('route:profile', function (userId) {
		  var userModel = options.userModel;
		  var currentUserModel = options.currentUserModel;
		  userModel.id = userId;
		  currentUserModel.fetch({
		    success: function(model, xhr){
		      // console.log('user:', model, xhr);
		      if(!xhr.user_id){
		        // console.log('not found user:', window.Passport.BASE_URL + '/login?next=' + window.location.href);
		        window.location = window.Passport.BASE_URL + '/login?next=' + window.location.href;
		      }
		    }
		  });
		  userModel.fetch();
		  
		  console.log('show profile of userId:', userId);
		  window.Passport.userId = userId;
		  options.activityCollection.fetch();
		  options.achievementCollection.fetch();
			require(['views/profile/page'], function (ProfilePage) {
				var profilePage = Vm.create(appView, 'ProfilePage', ProfilePage, {
				  userModel: userModel,
				  currentUserModel: currentUserModel,
				  activityCollection: options.activityCollection,
				  achievementCollection: options.achievementCollection
				});
				profilePage.render();
			});
		});
		router.on('route:defaultAction', function (actions) {
			console.log('default view');
		});
		
    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
