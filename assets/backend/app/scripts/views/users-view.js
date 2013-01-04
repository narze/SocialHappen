(function() {

  define(['backbone', 'text!templates/users-template.html', 'views/user-item-view'], function(Backbone, UsersTemplate, UserItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'users-view',
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listUsers);
        return this.collection.fetch();
      },
      listUsers: function() {
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
        this.listUsers();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
