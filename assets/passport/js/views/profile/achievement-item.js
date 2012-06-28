define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/achievement-item.html'
], function($, _, Backbone, achievementItemTemplate){
  var AchievementItem = Backbone.View.extend({
    tagName: 'li',
    achievementItemTemplate: _.template(achievementItemTemplate),
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      if(this.model.get('_id')){
        $(this.el).html(this.achievementItemTemplate(this.model.toJSON()));
      }
      return this;
    }
  });
  return AchievementItem;
});
