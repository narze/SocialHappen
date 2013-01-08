(function() {

  define(['backbone', 'text!templates/users-template.html', 'views/user-item-view'], function(Backbone, UsersTemplate, UserItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'users-view',
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
        this.collection.bind('reset', this.listUsers);
        this.collection.bind('change', this.listUsers);
        return this.collection.fetch();
      },
      listUsers: function() {
        this.$('#user-list').empty();
        this.collection.each(function(model) {
          return this.addUser(model);
        }, this);
        return this.pagination();
      },
      addUser: function(model) {
        var user;
        user = new UserItemView({
          model: model
        });
        this.subViews['user-' + model.cid] = user;
        return this.$('#user-list').append(user.render().el);
      },
      pagination: function() {
        return this.$('.users-pagination').html(_.template(this.$('#users-pagination-template').html(), this.collection.info()));
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
        this.$el.html(UsersTemplate);
        this.delegateEvents();
        this.listUsers();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
