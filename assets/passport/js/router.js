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
		  userModel.id = userId;
		  
		  userModel.fetch();
		  
		  console.log('show profile of userId:', userId);
		  		  
			require(['views/profile/page'], function (ProfilePage) {
				var profilePage = Vm.create(appView, 'ProfilePage', ProfilePage, {
				  userModel: userModel
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
