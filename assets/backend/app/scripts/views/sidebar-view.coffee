define [
  'backbone'
  'text!templates/sidebar-template.html'
], (Backbone, SidebarTemplate) ->
  View = Backbone.View.extend
    className: 'span2 main-menu-span'
    initialize: ->
    render: ->
      @$el.html SidebarTemplate
      @
