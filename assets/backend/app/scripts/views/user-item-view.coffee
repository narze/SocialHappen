define [
  'backbone'
  'moment'
  'text!templates/user-item-template.html'
  ], (Backbone, moment, UserItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'user-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', 'render'

    render: ->
      @$el.html _.template(UserItemTemplate, @model.toJSON())
      @

  View