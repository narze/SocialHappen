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
      '/profile/:id/coupons/:couponId': 'couponLanding',
      '/profile/:id/myprofile': 'myProfile',
      '/profile/:id/photos': 'photos',
      '/profile/:id/feedbacks': 'feedbacks',
      '/profile/:id/badges': 'badges',
      '/profile/:id/card': 'myCard',
      '/profile/:id/coupon': 'myCoupon',
      '/profile/:id/coupon/:id': 'myCouponItem',
      '/profile/:id/activity': 'activity',

      // Default - catch all
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
    var appView = options.appView;
    var router = new AppRouter(options);
    var page = null;
    // Check login : Fetch current user
    var currentUserModel = options.currentUserModel;
    currentUserModel.fetch({
      success: function(model, xhr){
        if(!xhr.user_id){
          window.location = window.Passport.BASE_URL + '/login?next=' + window.location.href
          return
        }
      }
    })

    router.on('route:profile', viewMyProfile);
    router.on('route:myProfile', viewMyProfile);
    router.on('route:photos', viewPhotos);
    router.on('route:feedbacks', viewFeedbacks);
    router.on('route:badges', viewBadges);
    router.on('route:myCard', viewMyCard);
    router.on('route:myCoupon', viewMyCoupon);
    router.on('route:myCouponItem', viewMyCouponItem);
    router.on('route:activity', viewActivity);

    function loadMainPage(viewOptions) {
      var userModel = options.userModel;
      var userId = viewOptions.userId;
      userModel.id = userId;
      userModel.fetch();
      console.log('show profile of userId:', userId);
      window.Passport.userId = userId;
      viewOptions.userModel = userModel;

      viewOptions.activityCollection = options.activityCollection;
      viewOptions.achievementCollection = options.achievementCollection;
      viewOptions.couponCollection = options.couponCollection;
      viewOptions.actionCollection = options.actionCollection;

      viewOptions.activityCollection.fetch();
      viewOptions.achievementCollection.fetch();
      viewOptions.couponCollection.fetch();
      viewOptions.actionCollection.fetch();

      viewOptions.currentUserModel = currentUserModel;
      viewOptions.vent = options.vent;

      require(['views/profile/page'], function (ProfilePage) {
        if(!page) {
          page = Vm.create(appView, 'ProfilePage', ProfilePage, viewOptions);
        } else {
          page.options = viewOptions;
        }
        page.render();
        page[viewOptions.load]();
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


    router.on('route:couponLanding', function(userId, couponId) {
      var userModel = options.userModel
      userModel.id = userId
      window.Passport.userId = userId

      options.couponCollection.fetch({
        success: function(collection, xhr) {
          console.log(collection)
          require(['views/profile/coupon'], function(CouponPage) {
            var couponPage = Vm.create(appView, 'CouponPage', CouponPage, {
              couponModel: collection.get(couponId),
              userModel: userModel,
              currentUserModel: currentUserModel
            })
            couponPage.render()
          })
        }
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
