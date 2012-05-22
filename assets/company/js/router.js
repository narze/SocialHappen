// Filename: router.js
define([
  'jquery',
  'underscore',
  'backbone',
  'vm'
], function ($, _, Backbone, Vm) {
  var AppRouter = Backbone.Router.extend({
    routes: {
      // Default - catch all
      '/company/:id': 'company',
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
    var appView = options.appView;
    var router = new AppRouter(options);
    router.on('route:defaultAction', function () {
      var currentUserModel = options.currentUserModel;
      currentUserModel.fetch({
        success: function(model, xhr){
          // console.log('user:', model, xhr);
          if(!xhr.user_id){
            // console.log('not found user:', window.Passport.BASE_URL + '/login?next=' + window.location.href);
            window.location = window.Company.BASE_URL + '/login?next=' + window.location.href;
          }
        }
      });
      
      options.challengesCollection.fetch();
      require(['views/company/page'], function (WorldPage) {
        var worldPage = Vm.create(appView, 'CompanyPage', WorldPage, {
          currentUserModel: currentUserModel,
          challengesCollection: options.challengesCollection,
          vent: options.vent
        });
        worldPage.render();
      });
    });
    
    router.on('route:company', function (companyId) {
      
      // console.log('show company:', companyId);
      
      window.Company.companyId = companyId;
      
      options.challengesCollection.url = window.Company.BASE_URL + '/apiv3/challenges/?company_id=' + companyId;
      
      var currentUserModel = options.currentUserModel;
      currentUserModel.fetch({
        success: function(model, xhr){
          // console.log('user:', model, xhr);
          if(!xhr.user_id){
            // console.log('not found user:', window.Passport.BASE_URL + '/login?next=' + window.location.href);
            window.location = window.Company.BASE_URL + '/login?next=' + window.location.href;
          }
        }
      });
      
      options.challengesCollection.fetch();
      require(['views/company/page'], function (WorldPage) {
        var worldPage = Vm.create(appView, 'CompanyPage', WorldPage, {
          currentUserModel: currentUserModel,
          challengesCollection: options.challengesCollection,
          vent: options.vent
        });
        worldPage.render();
      });
    });
    
    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
