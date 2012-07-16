define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/page.html',
  'text!templates/profile/action.html',
  'text!templates/profile/action-item.html',
  'views/profile/profile',
  'views/profile/activity-list',
  'views/profile/achievement-list',
  'views/profile/coupon-list'
], function($, _, Backbone, profilePageTemplate, actionListTemplate, actionItemTemplate, ProfilePane, ActivityListView, AchievementListView, CouponListView){
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
        // 'click .user-submenu-rewards': 'showRewardsList',
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
    getUserActionData: function(action_id) {

      var ajax_options = {
        url: window.Passport.BASE_URL + 'apiv3/userActionData',
        data: {
          action_id: action_id
        },
        dataType: 'json',
        success: function (data) {
          console.log(data);

          _.each(data, function(action) {
            var li = $('<li></li>').html($(actionItemTemplate).clone());
            var title = action.user_data.user_feedback;
            li.find('.action-title').html(title);
            li.find('.action-msg').html(action.message);
            $('.action-list').append(li);
          });
        }
      };

      //Coupon list could be seen only for current user
      if(this.options.currentUserModel.get('user_id') !== window.Passport.userId) {
        $.ajax(ajax_options);
      } else {
        //Fetch again and check
        var self = this;
        this.options.currentUserModel.fetch({
          success: function(model, xhr) {
            if(xhr.user_id && (xhr.user_id === window.Passport.userId)) {
              $.ajax(ajax_options);
            }
          }
        });
      }
    },
    showMyProfileList: function(action_id) {
      $('.user-right-pane', this.el).html($(actionListTemplate).clone());
      this.getUserActionData();
    },
    showPhotosList: function() {
      $('.user-right-pane', this.el).html($(actionListTemplate).clone());
      $('.header-sub', this.el).text('Photos');
      this.getUserActionData(9999);
    },
    showFeedbacksList: function() {
      $('.user-right-pane', this.el).html($(actionListTemplate).clone());
      $('.header-sub', this.el).text('Feedbacks');
      this.getUserActionData(202);
    },
    showBadgesList: function() {
      var achievementListView = new AchievementListView({
        collection: this.options.achievementCollection,
        el: $('.user-right-pane', this.el)
      });

      achievementListView.render();
    },
    // showRewardsList: function() {
    //   $('.user-right-pane', this.el).html($(actionListTemplate).clone());
    //   $('.header-sub', this.el).text('Rewards');
    //   this.getUserActionData(119);
    // },
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
