define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/activity-item'
], function($, _, Backbone, ActivityItemView){
  var ProfilePage = Backbone.View.extend({
    
    initialize: function(){
      _.bindAll(this);
      this.collection.bind('add', this.addOne);
      this.collection.bind('reset', this.addAll);
    },
    render: function () {
      this.addAll();
      return this;
    },
    
    addOne: function(model){
      var activityItemView = new ActivityItemView({
        model: model
      });
      
      this.$el.append(activityItemView.render().el);
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
