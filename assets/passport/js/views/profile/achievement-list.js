define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/achievement-item',
  'text!templates/profile/achievement.html',
  'sandbox'
], function($, _, Backbone, AchievementItemView, achievementListTemplate, sandbox){
  var AchievementList = Backbone.View.extend({
    achievementListTemplate: _.template(achievementListTemplate),
    initialize: function(){
      _.bindAll(this);
      sandbox.collections.achievementCollection.bind('add', this.addOne);
      sandbox.collections.achievementCollection.bind('reset', this.render);
      sandbox.collections.achievementCollection.fetch();
    },
    render: function () {
      this.$el.html(this.achievementListTemplate({
        total: sandbox.collections.achievementCollection.length
      }));

      this.addAll();
      return this;
    },

    addOne: function(model){
      var achievementItemView = new AchievementItemView({
        model: model
      });

      $('ul.achievement-list', this.$el).append(achievementItemView.render().el);
    },

    addAll: function(){
      var self = this;

      if(sandbox.collections.achievementCollection.models.length === 0){
        this.$('ul.achievement-list').html('No badge');
      }

      sandbox.collections.achievementCollection.each(function(model){
        self.addOne(model);
      });
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.collections.achievementCollection.unbind();
    }
  });
  return AchievementList;
});
