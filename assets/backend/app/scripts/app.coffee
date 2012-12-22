define ['main', 'models/company-model', 'collections/company-collection'], (Main, CompanyModel, CompanyCollection) ->
  window.backend.Models.CompanyModel = CompanyModel
  window.backend.Collections.CompanyCollection = new CompanyCollection
  window.appLoaded = true