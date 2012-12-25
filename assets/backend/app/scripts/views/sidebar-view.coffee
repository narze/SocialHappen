define [
  'backbone'
  'text!templates/sidebar-template.html'
], (Backbone, SidebarTemplate) ->
  View = Backbone.View.extend
    initialize: ->
    render: ->
      @$el.html SidebarTemplate
      @
