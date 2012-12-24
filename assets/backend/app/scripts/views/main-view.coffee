define [
  'backbone'
  'views/navbar-view'
  'views/sidebar-view'
  'views/content-view'
  ], (Backbone, NavBarView, SidebarView, ContentView) ->
  View = Backbone.View.extend {}
  window.backend.Views.NavBarView = new NavBarView
  window.backend.Views.SidebarView = new SidebarView
  window.backend.Views.ContentView = new ContentView
  View