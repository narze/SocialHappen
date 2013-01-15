(function() {

  define(['main', 'routes/main-router', 'views/main-view', 'views/users-view', 'views/activities-view', 'views/companies-view', 'views/challenges-view', 'models/company-model', 'collections/company-collection', 'models/user-model', 'collections/user-collection', 'models/activity-model', 'collections/activity-collection', 'models/challenge-model', 'collections/challenge-collection'], function(Main, MainRouter, MainView, UsersView, ActivitiesView, CompaniesView, ChallengesView, CompanyModel, CompanyCollection, UserModel, UserCollection, ActivityModel, ActivityCollection, ChallengeModel, ChallengeCollection) {
    var config, env;
    config = {
      dev: {
        baseUrl: 'https://socialhappen.dyndns.org/socialhappen/'
      },
      beta: {
        baseUrl: 'http://beta.socialhappen.com/'
      },
      production: {
        baseUrl: 'http://www.socialhappen.com/'
      }
    };
    if (window.location.href.match(/beta\.socialhappen\.com/)) {
      env = 'beta';
    } else if (window.location.href.match(/(\.)?socialhappen\.com/)) {
      env = 'production';
    } else {
      env = 'dev';
    }
    window.baseUrl = config[env].baseUrl;
    window.backend.Routers.MainRouter = new MainRouter;
    window.backend.Models.CompanyModel = CompanyModel;
    window.backend.Collections.CompanyCollection = new CompanyCollection;
    window.backend.Models.UserModel = UserModel;
    window.backend.Collections.UserCollection = new UserCollection;
    window.backend.Models.ActivityModel = ActivityModel;
    window.backend.Collections.ActivityCollection = new ActivityCollection;
    window.backend.Models.ChallengeModel = ChallengeModel;
    window.backend.Collections.ChallengeCollection = new ChallengeCollection;
    window.backend.Views.MainView = new MainView;
    window.backend.Views.MainView.render();
    window.backend.Views.UsersView = new UsersView({
      collection: window.backend.Collections.UserCollection
    });
    window.backend.Views.ActivitiesView = new ActivitiesView({
      collection: window.backend.Collections.ActivityCollection
    });
    window.backend.Views.CompaniesView = new CompaniesView({
      collection: window.backend.Collections.CompanyCollection
    });
    window.backend.Views.ChallengesView = new ChallengesView({
      collection: window.backend.Collections.ChallengeCollection
    });
    Backbone.history.start();
    return window.appLoaded = true;
  });

}).call(this);
