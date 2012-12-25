(function() {

  define(['main', 'routes/main-router', 'views/main-view', 'views/users-view', 'views/activities-view', 'models/company-model', 'collections/company-collection', 'models/user-model', 'collections/user-collection', 'models/activity-model', 'collections/activity-collection'], function(Main, MainRouter, MainView, UsersView, ActivitiesView, CompanyModel, CompanyCollection, UserModel, UserCollection, ActivityModel, ActivityCollection) {
    window.baseUrl = 'http://localhost/socialhappen/';
    window.backend.Routers.MainRouter = new MainRouter;
    window.backend.Models.CompanyModel = CompanyModel;
    window.backend.Collections.CompanyCollection = new CompanyCollection;
    window.backend.Models.UserModel = UserModel;
    window.backend.Collections.UserCollection = new UserCollection;
    window.backend.Models.ActivityModel = ActivityModel;
    window.backend.Collections.ActivityCollection = new ActivityCollection;
    window.backend.Views.MainView = new MainView;
    window.backend.Views.MainView.render();
    window.backend.Views.UsersView = new UsersView({
      collection: window.backend.Collections.UserCollection
    });
    window.backend.Views.ActivitiesView = new ActivitiesView({
      collection: window.backend.Collections.ActivityCollection
    });
    Backbone.history.start();
    return window.appLoaded = true;
  });

}).call(this);
