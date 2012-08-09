define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/activity-item',
  'text!templates/profile/activity.html',
  'sandbox'
], function($, _, Backbone, ActivityItemView, activityListTemplate, sandbox){
  var ProfilePage = Backbone.View.extend({
    el: '.user-right-pane',
    activityListTemplate: _.template(activityListTemplate),
    initialize: function(){
      _.bindAll(this);
      sandbox.collections.activityCollection.bind('add', this.addOne);
      sandbox.collections.activityCollection.bind('reset', this.render);
    },
    render: function () {
      this.$el.html(this.activityListTemplate({
        total: sandbox.collections.activityCollection.length
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
      if(sandbox.collections.activityCollection.models.length === 0){
        this.$('ul.activity-list').html('No activity');
      }

      sandbox.collections.activityCollection.each(function(model){
        self.addOne(model);
      });
    }
  });
  return ProfilePage;
});
