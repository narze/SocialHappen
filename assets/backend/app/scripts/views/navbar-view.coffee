define [
  'backbone'
  'text!templates/navbar-template.html'
], (Backbone, NavbarTemplate) ->
  View = Backbone.View.extend
    initialize: ->
    render: ->
      @$el.html NavbarTemplate
      @
