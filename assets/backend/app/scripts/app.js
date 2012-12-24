(function() {

  define(['main', 'routes/main-router', 'views/main-view', 'views/users-view', 'views/activities-view', 'models/company-model', 'collections/company-collection'], function(Main, MainRouter, MainView, UsersView, ActivitiesView, CompanyModel, CompanyCollection) {
    window.backend.Routers.MainRouter = new MainRouter;
    window.backend.Views.MainView = new MainView;
    window.backend.Views.MainView.render();
    window.backend.Views.UsersView = new UsersView;
    window.backend.Views.ActivitiesView = new ActivitiesView;
    window.backend.Models.CompanyModel = CompanyModel;
    window.backend.Collections.CompanyCollection = new CompanyCollection;
    Backbone.history.start();
    return window.appLoaded = true;
  });

}).call(this);
