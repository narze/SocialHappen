(function() {

  define(['backbone', 'text!templates/activities-template.html', 'views/activity-item-view'], function(Backbone, ActivitiesTemplate, ActivityItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'activities-view',
      events: {
        'click a.servernext': 'nextResultPage',
        'click a.serverprevious': 'previousResultPage',
        'click a.serverlast': 'gotoLast',
        'click a.page': 'gotoPage',
        'click a.serverfirst': 'gotoFirst'
      },
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listActivities);
        this.collection.bind('change', this.listActivities);
        return this.collection.fetch();
      },
      listActivities: function() {
        this.$('#activity-list').empty();
        this.collection.each(function(model) {
          return this.addActivity(model);
        }, this);
        return this.pagination();
      },
      addActivity: function(model) {
        var activity;
        activity = new ActivityItemView({
          model: model
        });
        this.subViews['activity-' + model.cid] = activity;
        return this.$('#activity-list').append(activity.render().el);
      },
      pagination: function() {
        return this.$('.activities-pagination').html(_.template(this.$('#activities-pagination-template').html(), this.collection.info()));
      },
      nextResultPage: function(e) {
        e.preventDefault();
        return this.collection.requestNextPage();
      },
      previousResultPage: function(e) {
        e.preventDefault();
        return this.collection.requestPreviousPage();
      },
      gotoFirst: function(e) {
        e.preventDefault();
        return this.collection.goTo(this.collection.information.firstPage);
      },
      gotoLast: function(e) {
        e.preventDefault();
        return this.collection.goTo(this.collection.information.lastPage);
      },
      gotoPage: function(e) {
        var page;
        e.preventDefault();
        page = $(e.target).text();
        return this.collection.goTo(page);
      },
      render: function() {
        this.$el.html(ActivitiesTemplate);
        this.delegateEvents();
        this.listActivities();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
