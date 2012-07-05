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
      'click .user-profile-nav>li>a': 'setMenuActive',
      'click .user-profile-nav ul>li>a': 'setSubMenuActive',
      'click .user-menu-my-profile': 'showMyProfileList',
        'click .user-submenu-photos': 'showPhotosList',
        'click .user-submenu-feedbacks': 'showFeedbacksList',
        'click .user-submenu-badges': 'showBadgesList',
        'click .user-submenu-rewards': 'showRewardsList',
      'click .user-menu-my-card': 'showMyCardList',
        'click .card': 'showCard',
      'click .user-menu-my-reward': 'showMyRewardList',
      'click .user-menu-activity': 'showActivityList'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      $(this.el).html(profilePageTemplate);
      $('div#header .passport').addClass('active');
      
      //Render profile pane
      var profilePane = new ProfilePane({
        el: $('.profile', this.el),
        userModel: this.options.userModel
      });
      profilePane.render();
      
      //Render right pane
      $('.user-menu-activity').parent().addClass('active');
      this.showActivityList();

      //Menu
      $('#badges-count').text();
      
    },
    setMenuActive: function(e) {
      e.preventDefault();
      $('.user-profile-nav li').removeClass('active');
      $(e.currentTarget).parent().addClass('active');
    },
    setSubMenuActive: function(e) {
      e.preventDefault();
      $('.user-profile-nav li').removeClass('active');
      $(e.currentTarget).parent().addClass('active').closest('ul').parent().addClass('active');
    },
    showMyProfileList: function() {
      
    },
    showPhotosList: function() {
      
    },
    showFeedbacksList: function() {
      
    },
    showBadgesList: function() {
      var achievementListView = new AchievementListView({
        collection: this.options.achievementCollection,
        el: $('.user-right-pane', this.el)
      });
      
      achievementListView.render();
    },
    showRewardsList: function() {
      
    },
    showMyCardList: function() {
      //Test template
      $.get('templates/profile/card-list.html', function (card) {
        card_template = $(card).find('.card').removeClass('open');
        $('.user-right-pane').html(card);
        $('.card-list').append(card_template.clone()).append(card_template.clone());
      });
    },
    showCard: function(e) {
      $(e.currentTarget).addClass('open').siblings().removeClass('open');
    },
    showMyRewardList: function() {
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
    },
    showActivityList: function() {
      var activityListView = new ActivityListView({
        collection: this.options.activityCollection,
        el: $('.user-right-pane', this.el)
      });
      activityListView.render();
    }
  });
  return ProfilePage;
});
