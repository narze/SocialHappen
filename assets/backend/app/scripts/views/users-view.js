(function() {

  define(['backbone', 'text!templates/users-template.html', 'views/users-filter-view', 'views/pagination-view', 'views/user-item-view'], function(Backbone, UsersTemplate, UsersFilterView, PaginationView, UserItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'users-view',
      events: {
        'click .sort-name': 'sort',
        'click .sort-signup-date': 'sort',
        'click .sort-last-seen': 'sort',
        'click .sort-points': 'sort'
      },
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listUsers);
        this.collection.bind('change', this.listUsers);
        return this.collection.fetch();
      },
      listUsers: function() {
        this.$('#user-list').empty();
        return this.collection.each(function(model) {
          return this.addUser(model);
        }, this);
      },
      addUser: function(model) {
        var user;
        user = new UserItemView({
          model: model
        });
        this.subViews['user-' + model.cid] = user;
        return this.$('#user-list').append(user.render().el);
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
        if ($target.hasClass('sort-name')) {
          this.collection.sort = 'user_first_name';
        } else if ($target.hasClass('sort-signup-date')) {
          this.collection.sort = 'user_register_date';
        } else if ($target.hasClass('sort-last-seen')) {
          this.collection.sort = 'user_last_seen';
        } else if ($target.hasClass('sort-points')) {
          this.collection.sort = 'points';
        }
        return this.collection.fetch();
      },
      render: function() {
        var i, paginationCount, _i, _j;
        this.$el.html(UsersTemplate);
        this.delegateEvents();
        this.listUsers();
        if (!this.subViews.filter) {
          this.subViews.filter = new UsersFilterView({
            collection: this.collection
          });
        }
        this.$('.users-filter-container').html(this.subViews.filter.render().el);
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
