define ['main', 'collections/company-collection'], (Main, CompanyCollection) ->
  window.backend =
    Models: {}
    Collections: {}
    Views: {}
    Routers: {}

  window.backend.Collections.CompanyCollection = CompanyCollection
  window.appLoaded = true