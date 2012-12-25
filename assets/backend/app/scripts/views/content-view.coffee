define [
  'backbone'
  'text!templates/content-template.html'
], (Backbone, ContentTemplate) ->
  View = Backbone.View.extend
    initialize: ->
    render: ->
      @$el.html ContentTemplate
      @
