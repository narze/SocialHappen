define [
  'backbone'
  'text!templates/main-template.html'
  'views/navbar-view'
  'views/sidebar-view'
  'views/content-view'
  ], (Backbone, MainTemplate, NavBarView, SidebarView, ContentView) ->

  View = Backbone.View.extend
    initialize: ->
    el: $('#app')
    render: ->
      @$el.html MainTemplate

      window.backend.Views.NavBarView = new NavBarView
      @$('#navbar-view').html(window.backend.Views.NavBarView.render().el)
      window.backend.Views.SidebarView = new SidebarView
      @$('#sidebar-view').html(window.backend.Views.SidebarView.render().el)
      window.backend.Views.ContentView = new ContentView
      @$('#content-view').html(window.backend.Views.ContentView.render().el)

      @rendered = true
      @


  View