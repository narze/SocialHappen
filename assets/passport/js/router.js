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
      '/profile/:id/coupons/:couponId': 'coupon',

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
          if(!xhr.user_id){
            window.location = window.Passport.BASE_URL + '/login?next=' + window.location.href;
          }
        }
      });
      userModel.fetch();

      console.log('show profile of userId:', userId);
      window.Passport.userId = userId;
      options.activityCollection.fetch();
      options.achievementCollection.fetch();
      options.couponCollection.fetch();
      options.actionCollection.fetch();

      require(['views/profile/page'], function (ProfilePage) {
        var profilePage = Vm.create(appView, 'ProfilePage', ProfilePage, {
          userModel: userModel,
          currentUserModel: currentUserModel,
          activityCollection: options.activityCollection,
          achievementCollection: options.achievementCollection,
          couponCollection: options.couponCollection,
          actionCollection: options.actionCollection,
          vent: options.vent
        });
        profilePage.render();
      });
    });

    router.on('route:coupon', function(userId, couponId) {
      var userModel = options.userModel
      var currentUserModel = options.currentUserModel
      userModel.id = userId
      window.Passport.userId = userId


      currentUserModel.fetch({
        success: function(model, xhr){
          if(!xhr.user_id){
            window.location = window.Passport.BASE_URL + '/login?next=' + window.location.href
            return
          }
        }
      })

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

    });

    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
