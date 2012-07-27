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
      '/company/:id/challenge': 'company',
      '/company/:id/reward': 'reward',
      '/company/:id/coupon': 'coupon',
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
    var appView = options.appView;
    var router = new AppRouter(options);

    var self = this;

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

    router.on('route:defaultAction', function () {

      options.challengesCollection.fetch();
      require(['views/company/page'], function (WorldPage) {
        var companyPage = Vm.create(appView, 'CompanyPage', WorldPage, {
          currentUserModel: currentUserModel,
          currentCompanyModel: options.currentCompanyModel,
          challengesCollection: options.challengesCollection,
          rewardsCollection: options.rewardsCollection,
          couponsCollection: options.couponsCollection,
          vent: options.vent
        });
        companyPage.render();
      });
    });

    router.on('route:company', function (companyId) {

      console.log('show company:', companyId);

      window.Company.companyId = companyId;

      options.challengesCollection.url = window.Company.BASE_URL + '/apiv3/challenges/?company_id=' + companyId;

      options.challengesCollection.fetch();

      options.currentCompanyModel.set('id', companyId, {
        silent: true
      });
      options.currentCompanyModel.fetch();

      if(!self.companyPage){
        require(['views/company/page'], function (WorldPage) {

          var companyPage = Vm.create(appView, 'CompanyPage', WorldPage, {
            currentUserModel: currentUserModel,
            currentCompanyModel: options.currentCompanyModel,
            challengesCollection: options.challengesCollection,
            rewardsCollection: options.rewardsCollection,
            couponsCollection: options.couponsCollection,
            vent: options.vent,
            now: 'challenge'
          });
          companyPage.render();
          self.companyPage = companyPage;

        });
      }else{
        self.companyPage.options.now = 'challenge';
        self.companyPage.render();
      }
    });

    router.on('route:reward', function (companyId) {

      console.log('show reward:', companyId);

      window.Company.companyId = companyId;

      options.rewardsCollection.url = window.Company.BASE_URL + '/apiv3/rewards/?company_id=' + companyId;

      options.rewardsCollection.fetch();

      options.currentCompanyModel.set('id', companyId, {
        silent: true
      });
      options.currentCompanyModel.fetch();

      if(!self.companyPage){
        require(['views/company/page'], function (WorldPage) {

          var companyPage = Vm.create(appView, 'CompanyPage', WorldPage, {
            currentUserModel: currentUserModel,
            currentCompanyModel: options.currentCompanyModel,
            challengesCollection: options.challengesCollection,
            rewardsCollection: options.rewardsCollection,
            couponsCollection: options.couponsCollection,
            vent: options.vent,
            now: 'reward'
          });
          companyPage.render();
          self.companyPage = companyPage;

        });
      }else{
        self.companyPage.options.now = 'reward';
        self.companyPage.render();
      }
    });

    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
