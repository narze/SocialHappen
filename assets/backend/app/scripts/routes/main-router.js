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
        return window.backend.Views.UsersView.render();
      },
      activities: function() {
        return window.backend.Views.ActivitiesView.render();
      },
      badRoute: function() {
        console.log('404 : Route not found');
        return this.notFound = true;
      }
    });
  });

}).call(this);
