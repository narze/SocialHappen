// Filename: router.js
define([
  'jquery',
  'underscore',
  'backbone',
  'vm'
], function ($, _, Backbone, Vm) {
  var AppRouter = Backbone.Router.extend({
    routes: {
      '/company/:id': 'companyWorld',

      // Default - catch all
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
    var appView = options.appView;
    var router = new AppRouter(options);
    
    router.on('route:defaultAction', function () {
      window.World.companyId = null;
      renderViews();
    });
    
    router.on('route:companyWorld', function (companyId) {
      window.World.companyId = companyId;
      renderViews();
    });

    function renderViews() {
      var currentUserModel = options.currentUserModel;
      currentUserModel.fetch({
        success: function(model, xhr){
          // console.log('user:', model, xhr);
          if(!xhr.user_id){
            // console.log('not found user:', window.Passport.BASE_URL + '/login?next=' + window.location.href);
            window.location = window.World.BASE_URL + '/login?next=' + window.location.href;
          }
        }
      });
      
      options.companiesCollection.fetch();
      options.challengesCollection.fetch();
      require(['views/world/page'], function (WorldPage) {
        var worldPage = Vm.create(appView, 'WorldPage', WorldPage, {
          currentUserModel: currentUserModel,
          challengesCollection: options.challengesCollection,
          companiesCollection: options.companiesCollection,
          vent: options.vent
        });
        worldPage.render();
      });
    }
    
    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
