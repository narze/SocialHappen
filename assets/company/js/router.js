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
      '/company/:id/users': 'users',
      '/company/:id/activities': 'activities',
      '/company/:id/coupon/:couponId': 'couponPopup',
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
        }else{
          checkCurrentUser();
        }
      }
    });

    function checkCurrentUser(){
      if(currentUserModel.get('user_id') && window.Company.companyId){
        var company = _.find(currentUserModel.get('companies'), function(i){
          return i.company_id === window.Company.companyId;
        });

        if(!company){
          window.location = window.Company.BASE_URL + 'passport';
        }
      }
    }

    router.on('route:defaultAction', function () {

      options.challengesCollection.fetch();
      require(['views/company/page'], function (CompanyPage) {
        var companyPage = Vm.create(appView, 'CompanyPage', CompanyPage, {
          currentUserModel: currentUserModel,
          challengesCollection: options.challengesCollection,
          rewardsCollection: options.rewardsCollection,
          couponsCollection: options.couponsCollection,
          companyUsersCollection: options.companyUsersCollection,
          activitiesCollection: options.activitiesCollection,
          vent: options.vent
        });
        companyPage.render();
      });
    });

    router.on('route:company', function (companyId) {
      console.log('show company:', companyId);

      window.Company.companyId = companyId;

      checkCurrentUser();

      options.challengesCollection.url = window.Company.BASE_URL + '/apiv3/challenges/?company_id=' + companyId;

      options.challengesCollection.fetch();
      if(!self.companyPage){
        require(['views/company/page'], function (CompanyPage) {

          var companyPage = Vm.create(appView, 'CompanyPage', CompanyPage, {
            currentUserModel: currentUserModel,
            challengesCollection: options.challengesCollection,
            rewardsCollection: options.rewardsCollection,
            couponsCollection: options.couponsCollection,
            companyUsersCollection: options.companyUsersCollection,
            activitiesCollection: options.activitiesCollection,
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

      if(!self.companyPage){
        require(['views/company/page'], function (CompanyPage) {

          var companyPage = Vm.create(appView, 'CompanyPage', CompanyPage, {
            currentUserModel: currentUserModel,
            challengesCollection: options.challengesCollection,
            rewardsCollection: options.rewardsCollection,
            couponsCollection: options.couponsCollection,
            companyUsersCollection: options.companyUsersCollection,
            activitiesCollection: options.activitiesCollection,
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

    router.on('route:coupon', couponRoute);
    router.on('route:couponPopup', couponRoute);

    function couponRoute(companyId, couponId) {
      console.log('show coupon:', companyId);

      window.Company.companyId = companyId;

      options.couponsCollection.url = window.Company.BASE_URL + '/apiv3/coupons/?company_id=' + companyId;

      options.couponsCollection.fetch();

      if(!self.companyPage){
        require(['views/company/page'], function (CompanyPage) {

          var companyPage = Vm.create(appView, 'CompanyPage', CompanyPage, {
            currentUserModel: currentUserModel,
            challengesCollection: options.challengesCollection,
            rewardsCollection: options.rewardsCollection,
            couponsCollection: options.couponsCollection,
            companyUsersCollection: options.companyUsersCollection,
            activitiesCollection: options.activitiesCollection,
            vent: options.vent,
            now: 'coupon'
          });
          companyPage.render();
          self.companyPage = companyPage;

          //Show coupon if couponId is set and exist
          if(couponId && options.couponsCollection.get(couponId)) { options.couponsCollection.get(couponId).trigger('view'); }
        });
      } else {
        self.companyPage.options.now = 'coupon';
        self.companyPage.render();
      }
    }

    router.on('route:activities', function(companyId) {
      console.log('show activities:', companyId);

      window.Company.companyId = companyId;

      options.activitiesCollection.url = window.Company.BASE_URL + '/apiv3/company_activities/' + companyId;

      options.activitiesCollection.fetch();

      if(!self.companyPage){
        require(['views/company/page'], function (CompanyPage) {

          var companyPage = Vm.create(appView, 'CompanyPage', CompanyPage, {
            currentUserModel: currentUserModel,
            challengesCollection: options.challengesCollection,
            rewardsCollection: options.rewardsCollection,
            couponsCollection: options.couponsCollection,
            companyUsersCollection: options.companyUsersCollection,
            activitiesCollection: options.activitiesCollection,
            vent: options.vent,
            now: 'activities'
          });
          companyPage.render();
          self.companyPage = companyPage;
        });
      } else {
        self.companyPage.options.now = 'activities';
        self.companyPage.render();
      }
    });

    router.on('route:users', function(companyId) {
      console.log('show users:', companyId);

      window.Company.companyId = companyId;

      options.companyUsersCollection.url = window.Company.BASE_URL + '/apiv3/company_users/' + companyId;

      options.companyUsersCollection.fetch();

      if(!self.companyPage){
        require(['views/company/page'], function (CompanyPage) {

          var companyPage = Vm.create(appView, 'CompanyPage', CompanyPage, {
            currentUserModel: currentUserModel,
            challengesCollection: options.challengesCollection,
            rewardsCollection: options.rewardsCollection,
            couponsCollection: options.couponsCollection,
            companyUsersCollection: options.companyUsersCollection,
            activitiesCollection: options.activitiesCollection,
            vent: options.vent,
            now: 'users'
          });
          companyPage.render();
          self.companyPage = companyPage;
        });
      } else {
        self.companyPage.options.now = 'users';
        self.companyPage.render();
      }
    });

    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
