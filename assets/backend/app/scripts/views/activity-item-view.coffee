define [
  'backbone'
  'moment'
  'text!templates/activity-item-template.html'
  ], (Backbone, moment, ActivityItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'activity-item'

    events:
      'click .audit-tooltip': 'void'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    void: (e) ->
      e.preventDefault()

    render: ->
      @$el.html _.template(ActivityItemTemplate, @model.toJSON())
      @delegateEvents()

      @$('.audit-tooltip').tooltip()

      @

  View