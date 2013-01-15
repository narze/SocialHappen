(function() {

  define(['backbone'], function(Backbone) {
    var Router;
    return Router = Backbone.Router.extend({
      routes: {
        '': 'users',
        'users': 'users',
        'activities': 'activities',
        'companies': 'companies',
        'challenges': 'challenges',
        '*other': 'badRoute'
      },
      users: function() {
        $('#content').html(window.backend.Views.UsersView.render().el);
        $('#sidebar-view .main-menu li').removeClass('active');
        return $('#sidebar-view .main-menu li.users-tab-menu').addClass('active');
      },
      activities: function() {
        $('#content').html(window.backend.Views.ActivitiesView.render().el);
        $('#sidebar-view .main-menu li').removeClass('active');
        return $('#sidebar-view .main-menu li.activities-tab-menu').addClass('active');
      },
      companies: function() {
        $('#content').html(window.backend.Views.CompaniesView.render().el);
        $('#sidebar-view .main-menu li').removeClass('active');
        return $('#sidebar-view .main-menu li.companies-tab-menu').addClass('active');
      },
      challenges: function() {
        $('#content').html(window.backend.Views.ChallengesView.render().el);
        $('#sidebar-view .main-menu li').removeClass('active');
        return $('#sidebar-view .main-menu li.challenges-tab-menu').addClass('active');
      },
      badRoute: function() {
        console.log('404 : Route not found');
        return this.notFound = true;
      }
    });
  });

}).call(this);
