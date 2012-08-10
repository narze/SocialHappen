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
    },

    render: function () {
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
      $('.user-profile-nav li').removeClass('active');
      $(e.currentTarget).parent().addClass('active').closest('ul').parent().addClass('active');
    }
  });
  return ProfilePage;
});
