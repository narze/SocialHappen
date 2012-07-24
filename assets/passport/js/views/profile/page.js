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
  'views/profile/coupon-list',
  'views/profile/card-list',
  'views/profile/action-list'
], function($, _, Backbone, profilePageTemplate, actionListTemplate, actionItemTemplate, ProfilePane, ActivityListView, AchievementListView, CouponListView, CardListView, ActionListView){
  var ProfilePage = Backbone.View.extend({
    profilePageTemplate: _.template(profilePageTemplate),
    el: '#content',

    events: {
      'click .user-profile-nav>li>a': 'setMenuActive',
      'click .user-profile-nav ul>li>a': 'setSubMenuActive'
      // 'click .user-menu-my-profile': 'showActionList',
      // // 'click .user-submenu-photos': 'showPhotosList',
      // 'click .user-submenu-feedbacks': 'showFeedbacksList',
      // 'click .user-submenu-badges': 'showBadgesList',
      // 'click .user-menu-my-card': 'showMyCardList',
      // 'click .user-menu-my-reward': 'showMyRewardList',
      // 'click .user-menu-activity': 'showActivityList'
    },

    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      $(this.el).html(this.profilePageTemplate({
        userId: this.options.userId
      }));
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
      // e.preventDefault();
      $('.user-profile-nav li').removeClass('active');
      $(e.currentTarget).parent().addClass('active');
    },
    setSubMenuActive: function(e) {
      // e.preventDefault();
      $('.user-profile-nav li').removeClass('active');
      $(e.currentTarget).parent().addClass('active').closest('ul').parent().addClass('active');
    },
    showBadgesList: function() {
      var achievementListView = new AchievementListView({
        collection: this.options.achievementCollection,
        el: $('.user-right-pane', this.el)
      });

      achievementListView.render();
    },
    showMyCardList: function() {
      var cardListView = new CardListView ({
        el: $('.user-right-pane', this.el),
        currentUserModel: this.options.currentUserModel,
        vent: this.options.vent
      })

      cardListView.render()
    },
    showMyRewardList: function(callback) {
      var couponListView = new CouponListView({
        collection: this.options.couponCollection,
        el: $('.user-right-pane', this.el),
        vent: this.options.vent
      });

      //Coupon list could be seen only for current user
      if(this.options.currentUserModel.get('user_id') === window.Passport.userId) {
        couponListView.render();
        if(typeof callback === 'function') { callback() }
      } else {
        //Fetch again and check
        this.options.currentUserModel.fetch({
          success: function(model, xhr) {
            if(xhr.user_id && (xhr.user_id === window.Passport.userId)) {
              couponListView.render();
              if(typeof callback === 'function') { callback() }
            }
          }
        });
      }
    },
    showMyCouponItem: function() {
      var self = this;
      //Show the list first
      this.showMyRewardList(showItem);

      function showItem() {
        if(self.options.couponCollection.isFetched) {
          var rewardItemId = self.options.rewardItemId;
          //Find the model
          //@TODO - sometimes couponcollection is not fetched yet, use setTimeout for now
          var model = _.find(self.options.couponCollection.models, function(model) { return model.get('reward_item_id') === rewardItemId; });
          //Trigger coupon-item.js event
          self.options.vent.trigger('viewRewardByModel', model);

        } else {
          console.log('coupons not fetched yet');
          setTimeout(showItem, 500);
        }
      }
    },
    showActivityList: function() {
      var activityListView = new ActivityListView({
        collection: this.options.activityCollection,
        el: $('.user-right-pane', this.el)
      });
      activityListView.render();
    },
    showFeedbacksList: function() {
      var actionListView = new ActionListView({
        collection: this.options.actionCollection,
        el: $('.user-right-pane', this.el),
        filter: 202,
        header_text: 'Feedbacks'
      });
      actionListView.render();
    },
    showPhotosList: function() {
      var actionListView = new ActionListView({
        collection: this.options.actionCollection,
        el: $('.user-right-pane', this.el),
        filter: 9999,
        header_text: 'Photos'
      });
      actionListView.render();
    },
    showActionList: function() {
      var actionListView = new ActionListView({
        collection: this.options.actionCollection,
        el: $('.user-right-pane', this.el)
      });
      actionListView.render();
    }
  });
  return ProfilePage;
});
