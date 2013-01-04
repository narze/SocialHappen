define [
  'backbone'
  'text!templates/navbar-template.html'
], (Backbone, NavbarTemplate) ->
  View = Backbone.View.extend
    className: 'navbar'
    initialize: ->
    render: ->
      @$el.html NavbarTemplate
      @
