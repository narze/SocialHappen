define [
  'backbone'
  'text!templates/content-template.html'
], (Backbone, ContentTemplate) ->
  View = Backbone.View.extend
    id: 'content'
    className: 'span10'
    initialize: ->
    render: ->
      @$el.html ContentTemplate
      @
