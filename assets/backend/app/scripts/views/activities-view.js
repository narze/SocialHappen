(function() {

  define(['backbone', 'text!templates/activities-template.html', 'views/activities-filter-view', 'views/pagination-view', 'views/activity-item-view'], function(Backbone, ActivitiesTemplate, ActivitiesFilterView, PaginationView, ActivityItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'activities-view',
      events: {
        'click .sort-date': 'sort'
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
      sort: function(e) {
        var $target;
        e.preventDefault();
        $target = $(e.currentTarget);
        if ($target.hasClass('sort-asc')) {
          $target.removeClass('sort-asc');
          $target.addClass('sort-desc');
          $target.removeClass('icon-chevron-up').addClass('icon-chevron-down');
          this.collection.order = '-';
        } else {
          $target.removeClass('sort-desc');
          $target.addClass('sort-asc');
          $target.removeClass('icon-chevron-down').addClass('icon-chevron-up');
          this.collection.order = '+';
        }
        this.collection.sort = 'timestamp';
        return this.collection.fetch();
      },
      render: function() {
        var i, paginationCount, _i, _j;
        this.$el.html(ActivitiesTemplate);
        this.delegateEvents();
        this.listActivities();
        if (!this.subViews.filter) {
          this.subViews.filter = new ActivitiesFilterView({
            collection: this.collection
          });
        }
        this.$('.activities-filter-container').html(this.subViews.filter.render().el);
        paginationCount = this.$('.pagination-container').length;
        if (paginationCount) {
          if (!this.subViews.pagination) {
            this.subViews.pagination = [];
            for (i = _i = 0; 0 <= paginationCount ? _i <= paginationCount : _i >= paginationCount; i = 0 <= paginationCount ? ++_i : --_i) {
              this.subViews.pagination[i] = new PaginationView({
                collection: this.collection
              });
            }
          }
          for (i = _j = 0; 0 <= paginationCount ? _j <= paginationCount : _j >= paginationCount; i = 0 <= paginationCount ? ++_j : --_j) {
            this.$('.pagination-container:eq(' + i + ')').html(this.subViews.pagination[i].render().el);
          }
        }
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
