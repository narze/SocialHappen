define [
  'backbone'
  'moment'
  'text!templates/activity-item-template.html'
  ], (Backbone, moment, ActivityItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'activity-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    render: ->
      @$el.html _.template(ActivityItemTemplate, @model.toJSON())
      @

  View