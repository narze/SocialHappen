(function() {

  define(['main', 'models/company-model', 'collections/company-collection'], function(Main, CompanyModel, CompanyCollection) {
    window.backend.Models.CompanyModel = CompanyModel;
    window.backend.Collections.CompanyCollection = new CompanyCollection;
    return window.appLoaded = true;
  });

}).call(this);
