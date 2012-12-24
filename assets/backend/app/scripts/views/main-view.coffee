define [
  'backbone'
  'text!templates/main-template.html'
  'views/navbar-view'
  'views/sidebar-view'
  'views/content-view'
  ], (Backbone, MainTemplate, NavBarView, SidebarView, ContentView) ->

  View = Backbone.View.extend
    initialize: ->
      @render()
    el: $('#app')
    render: ->
      @$el.html MainTemplate
      @rendered = true

  window.backend.Views.NavBarView = new NavBarView
  window.backend.Views.SidebarView = new SidebarView
  window.backend.Views.ContentView = new ContentView

  View