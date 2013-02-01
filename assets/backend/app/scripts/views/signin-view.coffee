define [
  'backbone'
  'text!templates/signin-template.html'
  ], (Backbone, SigninTemplate) ->

  View = Backbone.View.extend
    initialize: ->
    el: $('#app')
    render: ->
      @$el.html SigninTemplate

      @rendered = true
      @

  View