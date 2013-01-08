(function() {

  define(['backbone', 'text!templates/activities-template.html', 'views/pagination-view', 'views/activity-item-view'], function(Backbone, ActivitiesTemplate, PaginationView, ActivityItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'activities-view',
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listActivities);
        this.collection.bind('change', this.listActivities);
        return this.collection.fetch();
      },
      listActivities: function() {
        this.$('#activity-list').empty();
        return this.collection.each(function(model) {
          return this.addActivity(model);
        }, this);
      },
      addActivity: function(model) {
        var activity;
        activity = new ActivityItemView({
          model: model
        });
        this.subViews['activity-' + model.cid] = activity;
        return this.$('#activity-list').append(activity.render().el);
      },
      render: function() {
        this.$el.html(ActivitiesTemplate);
        this.delegateEvents();
        this.listActivities();
        if (!this.subViews.pagination) {
          this.subViews.pagination = [];
          this.subViews.pagination[0] = new PaginationView({
            collection: this.collection
          });
          this.subViews.pagination[1] = new PaginationView({
            collection: this.collection
          });
        }
        this.$('.pagination-container:eq(0)').html(this.subViews.pagination[0].render().el);
        this.$('.pagination-container:eq(1)').html(this.subViews.pagination[1].render().el);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
