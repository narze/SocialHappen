define [
  'backbone'
  'text!templates/activity-item-template.html'
  ], (Backbone, ActivityItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'activity-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', 'render'

    render: ->
      @$el.html _.template(ActivityItemTemplate, @model.toJSON())
      @

  View