// Filename: router.js
define([
  'jquery',
  'underscore',
  'backbone',
  'vm',
  'sandbox'
], function ($, _, Backbone, Vm, sandbox) {
  var AppRouter = Backbone.Router.extend({
    routes: {
      // Pages
      '/profile/:id': 'profile',
      '/profile/:id/photos': 'photo',
      '/profile/:id/feedbacks': 'feedback',
      '/profile/:id/badges': 'badge',
      '/profile/:id/card': 'card',
      '/profile/:id/coupon': 'coupon',
      '/profile/:id/coupon/:rewardItemId': 'couponItem',
      '/profile/:id/activity': 'activity',
      '/profile/:id/coupons/:couponId': 'couponLanding',

      // Default - catch all
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
    var appView = options.appView;
    var router = new AppRouter(options);
    var profilePage = null;
    // Check login : Fetch current user
    sandbox.models.currentUserModel.fetch({
      success: function(model, xhr){
        if(!xhr.user_id){
          window.location = window.Passport.BASE_URL + '/login?next=' + window.location.href
          return
        }
      }
    })

    function createProfilePage(ProfilePage) {
      if(!profilePage)
        profilePage = Vm.create(appView, 'ProfilePage', ProfilePage);

      // don't fetch if same user
      if(sandbox.userId !== sandbox.models.userModel.id) {
        window.Passport.userId = sandbox.userId;
        sandbox.models.userModel.id = sandbox.userId;
        sandbox.models.userModel.fetch();
        profilePage.render();
      }
    }

    router.on('route:profile', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/action-list'], function (ProfilePage, ActionListPage) {
        createProfilePage(ProfilePage);
        var actionListPage = Vm.create(appView, 'RightPane', ActionListPage, { filter: false});
        $('#right').html(actionListPage.render().el);
      });
    });

    router.on('route:photo', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/action-list'], function (ProfilePage, ActionListPage) {
        createProfilePage(ProfilePage);
        var actionListPage = Vm.create(appView, 'RightPane', ActionListPage, { filter: 9999, header_text: 'Photos' });
        $('#right').html(actionListPage.render().el);
      });
    });

    router.on('route:feedback', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/action-list'], function (ProfilePage, ActionListPage) {
        createProfilePage(ProfilePage);
        var actionListPage = Vm.create(appView, 'RightPane', ActionListPage, { filter: 202, header_text: 'Feedbacks' });
        $('#right').html(actionListPage.render().el);
      });
    });

    router.on('route:badge', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/achievement-list'], function (ProfilePage, AchievementListPage) {
        createProfilePage(ProfilePage);
        var achievementListPage = Vm.create(appView, 'RightPane', AchievementListPage);
        $('#right').html(achievementListPage.render().el);
      });
    });

    router.on('route:card', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/card-list'], function (ProfilePage, CardPage) {
        createProfilePage(ProfilePage);
        var cardPage = Vm.create(appView, 'RightPane', CardPage);
        $('#right').html(cardPage.render().el);
      });
    });

    router.on('route:coupon', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/coupon-list'], function (ProfilePage, CouponListPage) {
        createProfilePage(ProfilePage);
        var couponListPage = Vm.create(appView, 'RightPane', CouponListPage);
        $('#right').html(couponListPage.render().el);
      });
    });

    router.on('route:couponItem', function (id, rewardItemId) {
      sandbox.userId = id;
      sandbox.rewardItemId = rewardItemId;
      require(['views/profile/page', 'views/profile/coupon-list', 'views/profile/coupon-item'], function (ProfilePage, CouponList, CouponItem) {
        createProfilePage(ProfilePage);
        var couponList = Vm.create(appView, 'RightPane', CouponList);
        $('#right').html(couponList.render().el);

        sandbox.collections.couponCollection.fetch({success: function() {
          couponList.showCouponModal(rewardItemId);
        }});
      });
    });

    router.on('route:activity', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/activity-list'], function (ProfilePage, ActivityPage) {
        createProfilePage(ProfilePage);
        var activityPage = Vm.create(appView, 'RightPane', ActivityPage);
        $('#right').html(activityPage.render().el);
      });
    });

    router.on('route:couponLanding', function (id, couponId) {
      sandbox.userId = id;
      sandbox.couponId = couponId;
      sandbox.models.userModel.id = id
      window.Passport.userId = id;

      require(['views/profile/coupon'], function(CouponPage) {
        var couponPage = Vm.create(appView, 'CouponPage', CouponPage)
        couponPage.render();
      })
    })

    router.on('route:defaultAction', function (actions) {
      console.log('Route not found : ', actions);
    });

    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
