(function() {

  define(['main', 'collections/company-collection'], function(Main, CompanyCollection) {
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
