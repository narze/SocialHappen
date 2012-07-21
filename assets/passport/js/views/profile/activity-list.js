define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/activity-item',
  'text!templates/profile/activity.html'
], function($, _, Backbone, ActivityItemView, activityListTemplate){
  var ProfilePage = Backbone.View.extend({
    activityListTemplate: _.template(activityListTemplate),
    initialize: function(){
      _.bindAll(this);
      this.collection.bind('add', this.addOne);
      this.collection.bind('reset', this.addAll);
    },
    render: function () {
      this.$el.html(this.activityListTemplate({
        total: this.collection.length
      }));

      this.addAll();
      return this;
    },

    addOne: function(model){
      var activityItemView = new ActivityItemView({
        model: model
      });

      $('ul.activity-list', this.$el).append(activityItemView.render().el);
    },

    addAll: function(){
      var self = this;
      this.collection.each(function(model){
        self.addOne(model);
      });
    }
  });
  return ProfilePage;
});
