define [
  'main'
  'routes/main-router'
  'views/main-view'
  'models/company-model'
  'collections/company-collection'
  ], (Main, MainRouter, MainView, CompanyModel, CompanyCollection) ->

  window.backend.Routers.MainRouter = new MainRouter

  window.backend.Views.MainView = new MainView

  window.backend.Models.CompanyModel = CompanyModel

  window.backend.Collections.CompanyCollection = new CompanyCollection

  window.appLoaded = true