(function() {

  define(['backbone'], function(Backbone) {
    var Router;
    return Router = Backbone.Router.extend({
      routes: {
        '': 'index',
        'users': 'users',
        'activities': 'activities',
        '*other': 'badRoute'
      },
      users: function() {
        return $('#content').html(window.backend.Views.UsersView.render().el);
      },
      activities: function() {
        return $('#content').html(window.backend.Views.ActivitiesView.render().el);
      },
      badRoute: function() {
        console.log('404 : Route not found');
        return this.notFound = true;
      }
    });
  });

}).call(this);
