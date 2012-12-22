(function() {

  define(['backbone', 'collections/company-collection'], function(Backbone, CompanyCollection) {
    window.backend = {
      Models: {},
      Collections: {},
      Views: {},
      Routers: {}
    };
    window.backend.Collections.CompanyCollection = CompanyCollection;
    return window.appLoaded = true;
  });

}).call(this);
