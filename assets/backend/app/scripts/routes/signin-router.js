(function() {

  define(['backbone'], function(Backbone) {
    var Router;
    return Router = Backbone.Router.extend({
      routes: {
        '': 'signin',
        '*other': 'badRoute'
      },
      signin: function() {
        $('#content').html(window.backend.Views.SigninView.render().el);
        $('#sidebar-view .main-menu li').removeClass('active');
        return $('#sidebar-view .main-menu li.users-tab-menu').addClass('active');
      },
      badRoute: function() {
        console.log('404 : Route not found');
        return this.notFound = true;
      }
    });
  });

}).call(this);