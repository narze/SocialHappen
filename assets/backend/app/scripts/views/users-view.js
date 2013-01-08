(function() {

  define(['backbone', 'text!templates/users-template.html', 'views/users-filter-view', 'views/pagination-view', 'views/user-item-view'], function(Backbone, UsersTemplate, UsersFilterView, PaginationView, UserItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'users-view',
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
