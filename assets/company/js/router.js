// Filename: router.js
define([
  'jquery',
  'underscore',
  'backbone',
  'vm',
  'events',
  'sandbox',
  'views/company/page'
], function ($, _, Backbone, Vm, vent, sandbox, CompanyPage) {
  var AppRouter = Backbone.Router.extend({
    routes: {
      // Default - catch all
      '/company/:id': 'company',
      '/company/:id/challenge': 'company',
      '/company/:id/reward': 'reward',
      '/company/:id/coupon': 'coupon',
      '/company/:id/users': 'users',
      '/company/:id/users/:userId': 'user',
      '/company/:id/activities': 'activities',
      '/company/:id/coupon/:couponId': 'couponPopup',
      '/create': 'createCompany',
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
    var appView = sandbox.views.appView = options.appView;
    var router = new AppRouter(options);
    var companyPage = null;
    var self = this;

    function createCompanyPage() {
      if(isCompanyUser()) {
        if(!companyPage) companyPage = Vm.create(appView, 'CompanyPage', CompanyPage);

        if(sandbox.companyId !== sandbox.tempCompanyId) {
          companyPage.render();
          sandbox.tempCompanyId = sandbox.companyId;
        }
      }
    }

    function isCompanyUser(){
      var company = _.find(sandbox.models.currentUserModel.get('companies'), function(i){
        return i.company_id === window.Company.companyId;
      });

      if(!company){
        window.location = window.Company.BASE_URL + 'passport';
        return false;
      }

      return true;
    }

    router.on('route:company', function(id) {
      sandbox.companyId = window.Company.companyId = id;
      sandbox.now = 'challenge';
      require(['views/company/challenge-list'], function(ChallengeList) {
        createCompanyPage();
        var challengeListView = Vm.create(sandbox.views.appView, 'Content', ChallengeList);
        $('#content-pane').html(challengeListView.render().el);
      })
    })

    router.on('route:reward', function(id) {
      sandbox.companyId = window.Company.companyId = id;
      sandbox.now = 'reward';
      sandbox.collections.rewardsCollection.url = window.Company.BASE_URL + '/apiv3/rewards/?company_id=' + id;
      require(['views/company/reward-list'], function(RewardList) {
        createCompanyPage();
        var rewardListView = Vm.create(sandbox.views.appView, 'Content', RewardList);
        $('#content-pane').html(rewardListView.render().el);
      })
    })

    router.on('route:coupon', function(id) {
      sandbox.companyId = window.Company.companyId = id;
      sandbox.now = 'coupon';
      sandbox.collections.couponsCollection.url = window.Company.BASE_URL + '/apiv3/coupons/?company_id=' + id;
      require(['views/company/coupon-list'], function(CouponList) {
        createCompanyPage();
        var couponListView = Vm.create(sandbox.views.appView, 'Content', CouponList);
        $('#content-pane').html(couponListView.render().el);
      })
    })

    router.on('route:couponPopup', function(id, couponId) {
      sandbox.companyId = window.Company.companyId = id;
      sandbox.now = 'coupon';
      sandbox.collections.couponsCollection.url = window.Company.BASE_URL + '/apiv3/coupons/?company_id=' + id;
      require(['views/company/coupon-list'], function(CouponList) {
        createCompanyPage();
        var couponListView = Vm.create(sandbox.views.appView, 'Content', CouponList);
        $('#content-pane').html(couponListView.render().el);

        sandbox.collections.couponsCollection.fetch({success: function() {
          if(sandbox.collections.couponsCollection.get(couponId)) { sandbox.collections.couponsCollection.get(couponId).trigger('view'); }
        }});
      })
    })

    router.on('route:activities', function(id) {
      sandbox.companyId = window.Company.companyId = id;
      sandbox.now = 'activities';
      sandbox.collections.activitiesCollection.url = window.Company.BASE_URL + '/apiv3/company_activities/' + id;
      require(['views/company/activity-list'], function(ActivityList) {
        createCompanyPage();
        var activityListView = Vm.create(sandbox.views.appView, 'Content', ActivityList);
        $('#content-pane').html(activityListView.render().el);
      })
    })

    router.on('route:users', function(id) {
      sandbox.companyId = window.Company.companyId = id;
      sandbox.now = 'users';
      sandbox.collections.companyUsersCollection.url = window.Company.BASE_URL + '/apiv3/company_users/' + id;
      require(['views/company/company-user-list'], function(CompanyUserList) {
        createCompanyPage();
        var companyUserListView = Vm.create(sandbox.views.appView, 'Content', CompanyUserList);
        $('#content-pane').html(companyUserListView.render().el);
      })
    })

    router.on('route:user', function(id, userId) {
      sandbox.companyId = window.Company.companyId = id;
      sandbox.now = 'users';
      sandbox.userId = userId;
      sandbox.collections.companyUsersCollection.url = window.Company.BASE_URL + '/apiv3/company_users/' + id;
      require(['views/company/company-user-list'], function(CompanyUserList) {
        createCompanyPage();
        var companyUserListView = Vm.create(sandbox.views.appView, 'Content', CompanyUserList);
        $('#content-pane').html(companyUserListView.render().el);

        sandbox.collections.companyUsersCollection.fetch({success: function() {
          if(sandbox.collections.companyUsersCollection.get(userId)) {
            sandbox.collections.companyUsersCollection.get(userId).trigger('view');
          }
        }})
      })
    })

    router.on('route:createCompany', function() {
      require(['views/company/create-company'], function(CreateCompany) {
        var createCompanyView = Vm.create(sandbox.views.appView, 'Content', CreateCompany);
        $('#content').html(createCompanyView.render().el);
      })
    })

    router.on('route:defaultAction', function (actions) {
      console.log('Route not found : ', actions);
    });

    Backbone.history.start();
  }

  return {
    initialize: initialize
  };
});
