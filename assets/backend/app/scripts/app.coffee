define ['backbone', 'collections/company-collection'], (Backbone, CompanyCollection) ->
  window.backend =
    Models: {}
    Collections: {}
    Views: {}
    Routers: {}

  window.backend.Collections.CompanyCollection = CompanyCollection
  window.appLoaded = true