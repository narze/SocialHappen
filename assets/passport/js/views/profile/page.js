define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/page.html',
  'views/profile/profile',
  'views/profile/activity-list',
  'views/profile/achievement-list'
], function($, _, Backbone, profilePageTemplate, ProfilePane, ActivityListView, AchievementListView){
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
      
      
    }
  });
  return ProfilePage;
});
