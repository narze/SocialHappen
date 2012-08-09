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
      '/profile/:id/myprofile': 'myProfile',
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
      sandbox.models.userModel.id = sandbox.userId;
      sandbox.models.userModel.fetch();
      profilePage.render();
    }

    ////template
    router.on('route:profile', function (id) {
      sandbox.userId = id;
      require(['views/profile/page'], function (ProfilePage) {
        createProfilePage(ProfilePage);
      });
    });

    router.on('route:myProfile', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/action-list'], function (ProfilePage, ActionListPage) {
        createProfilePage(ProfilePage);
        var actionListPage = Vm.create(appView, 'RightPane', ActionListPage, { filter: null });
        actionListPage.render();
      });
    });

    router.on('route:photo', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/action-list'], function (ProfilePage, ActionListPage) {
        createProfilePage(ProfilePage);
        var actionListPage = Vm.create(appView, 'RightPane', ActionListPage, { filter: 9999, header_text: 'Photos' });
        actionListPage.render();
      });
    });

    router.on('route:feedback', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/action-list'], function (ProfilePage, ActionListPage) {
        createProfilePage(ProfilePage);
        var actionListPage = Vm.create(appView, 'RightPane', ActionListPage, { filter: 202, header_text: 'Feedbacks' });
        actionListPage.render();
      });
    });

    router.on('route:badge', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/action-list'], function (ProfilePage, ActionListPage) {
        createProfilePage(ProfilePage);
        var actionListPage = Vm.create(appView, 'RightPane', ActionListPage);
        actionListPage.render();
      });
    });

    router.on('route:card', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/card-list'], function (ProfilePage, CardPage) {
        createProfilePage(ProfilePage);
        var cardPage = Vm.create(appView, 'RightPane', CardPage);
        cardPage.render();
      });
    });

    router.on('route:coupon', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/coupon-list'], function (ProfilePage, CouponListPage) {
        createProfilePage(ProfilePage);
        var couponListPage = Vm.create(appView, 'RightPane', CouponListPage);
        couponListPage.render();
      });
    });

    router.on('route:couponItem', function (id, rewardItemId) {
      sandbox.userId = id;
      sandbox.rewardItemId = rewardItemId;
      require(['views/profile/page', 'views/profile/coupon-item'], function (ProfilePage, CouponItemPage) {
        createProfilePage(ProfilePage);
        var couponItemPage = Vm.create(appView, 'RightPane', CouponItemPage);
        couponItemPage.render();
      });
    });

    router.on('route:activity', function (id) {
      sandbox.userId = id;
      require(['views/profile/page', 'views/profile/activity-list'], function (ProfilePage, ActivityPage) {
        createProfilePage(ProfilePage);
        var activityPage = Vm.create(appView, 'RightPane', ActivityPage);
        activityPage.render();
      });
    });

    router.on('route:coupons', function (id, couponId) {
      sandbox.userId = id;
      sandbox.couponId = couponId;
      sandbox.models.userModel.id = id
      window.Passport.userId = id;

      require(['views/profile/coupon'], function(CouponPage) {
        var couponPage = Vm.create(appView, 'CouponPage', CouponPage, {
          couponModel: collection.get(couponId)
        })
        couponPage.render();
        sandbox.collections.couponCollection.fetch();
      })
    })

    function loadMainPage(viewOptions) {
      var userModel = options.userModel;
      var userId = viewOptions.userId;

      window.Passport.userId = userId;

      options.achievementCollection.userId = userId;
      options.actionCollection.userId = userId;

      userModel.id = userId;
      userModel.fetch();
      viewOptions.userModel = userModel;
      viewOptions.userModel.trigger('change')

      viewOptions.activityCollection = options.activityCollection;
      viewOptions.achievementCollection = options.achievementCollection;
      viewOptions.couponCollection = options.couponCollection;
      viewOptions.actionCollection = options.actionCollection;

      if(!page) {
        viewOptions.activityCollection.fetch();
        viewOptions.achievementCollection.fetch();
        viewOptions.couponCollection.fetch();
        viewOptions.actionCollection.fetch();
      }

      viewOptions.currentUserModel = currentUserModel;
      viewOptions.vent = options.vent;

      require(['views/profile/page'], function (ProfilePage) {
        if(!page) {
          page = Vm.create(appView, 'ProfilePage', ProfilePage, viewOptions);
          page.render();
        } else {
          page.options = viewOptions;
        }
        page[viewOptions.load]();

        if(viewOptions.load === 'showMyRewardList'){
          $('a.user-menu-my-reward').parent().addClass('active');
        }else if(viewOptions.load === 'showActionList'){
          $('a.user-menu-my-profile').parent().addClass('active');
        }else if(viewOptions.load === 'showFeedbacksList'){
          $('a.user-menu-my-profile').parent().addClass('active');
        }else if(viewOptions.load === 'showBadgesList'){
          $('a.user-menu-my-profile').parent().addClass('active');
          $('a.user-submenu-badges').parent().addClass('active');
        }else if(viewOptions.load === 'showFeedbacksList'){
          $('a.user-menu-my-profile').parent().addClass('active');
          $('a.user-submenu-feedbacks').parent().addClass('active');
        }else if(viewOptions.load === 'showActivityList'){
          $('a.user-menu-activity').parent().addClass('active');
        }else if(viewOptions.load === 'showMyCouponItem'){
          $('a.user-menu-my-reward').parent().addClass('active');
        }else if(viewOptions.load === 'showMyCardList'){
          $('a.user-menu-my-card').parent().addClass('active');
        }
      });
    }

    function viewMyProfile(userId) {
      loadMainPage({
        userId: userId,
        load: 'showActionList'
      });
    }

    function viewPhotos(userId) {
      loadMainPage({
        userId: userId,
        load: 'showPhotosList'
      })
    }

    function viewFeedbacks(userId) {
      loadMainPage({
        userId: userId,
        load: 'showFeedbacksList'
      })
    }

    function viewBadges(userId) {
      loadMainPage({
        userId: userId,
        load: 'showBadgesList'
      })
    }

    function viewMyCard(userId) {
      loadMainPage({
        userId: userId,
        load: 'showMyCardList'
      })
    }

    function viewMyCoupon(userId) {
      loadMainPage({
        userId: userId,
        load: 'showMyRewardList'
      })
    }

    function viewMyCouponItem(userId, rewardItemId) {
      loadMainPage({
        userId: userId,
        rewardItemId: rewardItemId,
        load: 'showMyCouponItem'
      })
    }

    function viewActivity(userId) {
      loadMainPage({
        userId: userId,
        load: 'showActivityList'
      })
    }

    router.on('route:defaultAction', function (actions) {
      console.log('Route not found : ', actions);
    });

    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
