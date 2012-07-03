define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/page.html',
  'views/profile/profile',
  'views/profile/activity-list',
  'views/profile/achievement-list',
  'views/profile/coupon-list'
], function($, _, Backbone, profilePageTemplate, ProfilePane, ActivityListView, AchievementListView, CouponListView){
  var ProfilePage = Backbone.View.extend({
    profilePageTemplate: _.template(profilePageTemplate),
    el: '#content',

    events: {
      'click .user-menu-my-profile': 'render',
        'click .user-submenu-photos': 'render',
        'click .user-submenu-feedbacks': 'render',
        'click .user-submenu-badges': 'showAchievementList',
        'click .user-submenu-rewards': 'render',
      'click .user-menu-my-card': 'render',
      'click .user-menu-my-reward': 'showCouponList',
      'click .user-menu-activity': 'showActivityList'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      $(this.el).html(profilePageTemplate);
      $('div#header .passport').addClass('active');
      
      var profilePane = new ProfilePane({
        el: $('.profile', this.el),
        userModel: this.options.userModel
      });
      profilePane.render();
      
      $('.user-menu-activity').click();
      
    },
    showActivityList: function(e) {
      e.preventDefault();
      $('.user-menu-activity').parent().addClass('active').siblings().removeClass('active');
      var activityListView = new ActivityListView({
        collection: this.options.activityCollection,
        el: $('.user-right-pane', this.el)
      });
      
      activityListView.render();
    },
    showAchievementList: function(e) {
      e.preventDefault();
      $('.user-menu-my-profile').parent().addClass('active').siblings().removeClass('active');
      
      var achievementListView = new AchievementListView({
        collection: this.options.achievementCollection,
        el: $('.user-right-pane', this.el)
      });
      
      achievementListView.render();
    },
    showCouponList: function(e) {
      e.preventDefault();
      $('.user-menu-my-reward').parent().addClass('active').siblings().removeClass('active');

      var couponListView = new CouponListView({
        collection: this.options.couponCollection,
        el: $('.user-right-pane', this.el)
      });

      //Coupon list could be seen only for current user
      if(this.options.currentUserModel.get('user_id') === window.Passport.userId) {
        couponListView.render();
      } else {
        //Fetch again and check
        var self = this;
        this.options.currentUserModel.fetch({
          success: function(model, xhr) {
            if(xhr.user_id && (xhr.user_id === window.Passport.userId)) {
              couponListView.render();
            }
          }
        });
      }
    }
  });
  return ProfilePage;
});
