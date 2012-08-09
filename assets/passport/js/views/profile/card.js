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
  'views/profile/action-list',
  'events',
  'sandbox'
], function($, _, Backbone, profilePageTemplate, actionListTemplate, actionItemTemplate, ProfilePane, ActivityListView, AchievementListView, CouponListView, CardListView, ActionListView, vent, sandbox){
  var ProfilePage = Backbone.View.extend({
    profilePageTemplate: _.template(profilePageTemplate),
    el: '#content',

    events: {
      'click .user-profile-nav>li>a': 'setMenuActive',
      'click .user-profile-nav ul>li>a': 'setSubMenuActive'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.models.userModel.bind('change', this.render);
      sandbox.models.currentUserModel.bind('change', this.render);
    },

    render: function () {
      console.log(sandbox.models.userModel, sandbox.models.currentUserModel);
      $(this.el).html(this.profilePageTemplate({
        user: sandbox.models.userModel.toJSON(),
        userId: sandbox.userId,
        isCurrentUser: sandbox.models.userModel.id === sandbox.models.currentUserModel.get('user_id')
      }));

      $('div#header .passport').addClass('active');

      //Render profile pane
      var profilePane = new ProfilePane({
        el: $('.profile', this.el)
      });
      profilePane.render();
      console.log('page rendered');
      //Menu
      // $('#badges-count').text();

    },
    setMenuActive: function(e) {
      // e.preventDefault();
      // console.log($(e.currentTarget).parent());
      $('.user-profile-nav li').removeClass('active');
      $(e.currentTarget).parent().addClass('active');
    },
    setSubMenuActive: function(e) {
      // e.preventDefault();
      // console.log($(e.currentTarget).parent());
      $('.user-profile-nav li').removeClass('active');
      $(e.currentTarget).parent().addClass('active').closest('ul').parent().addClass('active');
    },
    showBadgesList: function() {
      var achievementListView = new AchievementListView({
        collection: sandbox.collections.achievementCollection,
        el: $('.user-right-pane', this.el)
      });

      achievementListView.render();
    },
    showMyCardList: function() {
      var cardListView = new CardListView ({
        el: $('.user-right-pane', this.el),
        currentUserModel: sandbox.models.currentUserModel,
        vent: vent
      })

      cardListView.render()
    },
    showMyRewardList: function(callback) {
      var couponListView = new CouponListView({
        collection: sandbox.collections.couponCollection,
        el: $('.user-right-pane', this.el),
        vent: vent
      });

      //Coupon list could be seen only for current user
      if(sandbox.models.currentUserModel.get('user_id') === window.Passport.userId) {
        couponListView.render();
        if(typeof callback === 'function') { callback() }
      } else {
        //Fetch again and check
        sandbox.models.currentUserModel.fetch({
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
        if(sandbox.collections.couponCollection.isFetched) {
          var rewardItemId = sandbox.rewardItemId;
          //Find the model
          //@TODO - sometimes couponcollection is not fetched yet, use setTimeout for now
          var model = _.find(sandbox.collections.couponCollection.models, function(model) { return model.get('reward_item_id') === rewardItemId; });
          //Trigger coupon-item.js event
          vent.trigger('viewRewardByModel', model);

        } else {
          console.log('coupons not fetched yet');
          setTimeout(showItem, 500);
        }
      }
    },
    showActivityList: function() {
      var activityListView = new ActivityListView({
        collection: sandbox.collections.activityCollection,
        el: $('.user-right-pane', this.el)
      });
      activityListView.render();
    },
    showFeedbacksList: function() {
      var actionListView = new ActionListView({
        collection: sandbox.collections.actionCollection,
        el: $('.user-right-pane', this.el),
        filter: 202,
        header_text: 'Feedbacks'
      });
      actionListView.render();
    },
    showPhotosList: function() {
      var actionListView = new ActionListView({
        collection: sandbox.collections.actionCollection,
        el: $('.user-right-pane', this.el),
        filter: 9999,
        header_text: 'Photos'
      });
      actionListView.render();
    },
    showActionList: function() {
      var actionListView = new ActionListView({
        collection: null,
        el: $('.user-right-pane', this.el)
      });
      actionListView.render();
    }
  });
  return ProfilePage;
});
