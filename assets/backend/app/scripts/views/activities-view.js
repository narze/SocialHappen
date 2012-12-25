(function() {

  define(['backbone', 'text!templates/activities-template.html', 'views/activity-item-view'], function(Backbone, ActivitiesTemplate, ActivityItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'activities-view',
      initialize: function() {
        _.bindAll(this);
        this.collection.bind('reset', this.listActivities);
        return this.collection.fetch();
      },
      listActivities: function() {
        return this.collection.each(function(model) {
          return this.addActivity(model);
        }, this);
      },
      addActivity: function(model) {
        var activity;
        activity = new ActivityItemView({
          model: model
        });
        return this.$('#activity-list').append(activity.render().el);
      },
      render: function() {
        this.$el.html(ActivitiesTemplate);
        this.listActivities();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
