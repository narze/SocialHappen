(function() {

  define(['backbone', 'text!templates/users-template.html', 'views/pagination-view', 'views/user-item-view'], function(Backbone, UsersTemplate, PaginationView, UserItemView) {
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
        this.$el.html(UsersTemplate);
        this.delegateEvents();
        this.listUsers();
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
