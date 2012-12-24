define [
  'backbone'
  'text!templates/users-template.html'
  ], (Backbone, UsersTemplate) ->

  View = Backbone.View.extend
    id: 'users-view'
    initialize: ->
    render: ->
      @$el.html UsersTemplate
      @rendered = true
      @

  View