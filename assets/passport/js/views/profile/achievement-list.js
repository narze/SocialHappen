define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/achievement-item',
  'text!templates/profile/achievement.html'
], function($, _, Backbone, AchievementItemView, achievementListTemplate){
  var AchievementList = Backbone.View.extend({
    achievementListTemplate: _.template(achievementListTemplate),
    initialize: function(){
      _.bindAll(this);
      this.collection.bind('add', this.addOne);
      this.collection.bind('reset', this.render);
    },
    render: function () {
      this.$el.html(this.achievementListTemplate({
        total: this.collection.length
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

      if(this.collection.models.length == 0){
        this.$('ul.achievement-list').html('No badge');
      }

      this.collection.each(function(model){
        self.addOne(model);
      });
    }
  });
  return AchievementList;
});
