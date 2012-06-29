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
      
      var activityListView = new ActivityListView({
        collection: this.options.activityCollection,
        el: $('ul.activity-list', this.el)
      });
      
      activityListView.render();
      
      var achievementListView = new AchievementListView({
        collection: this.options.achievementCollection,
        el: $('div.achievement', this.el)
      });
      
      achievementListView.render();
      
      //Coupon list could be seen only for current user
      if(this.options.currentUserModel.get('user_id') === window.Passport.userId) {
        this.renderCouponList();
      } else {
        //Fetch again and check
        var self = this;
        this.options.currentUserModel.fetch({
          success: function(model, xhr) {
            if(xhr.user_id && (xhr.user_id === window.Passport.userId)) {
              self.renderCouponList();
            }
          }
        });
      }
    },
    renderCouponList: function() {
      var couponListView = new CouponListView({
        collection: this.options.couponCollection,
        el: $('div.coupon', this.el)
      });
      
      couponListView.render();
    }
  });
  return ProfilePage;
});
