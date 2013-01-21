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
      activity = @model.toJSON()

      switch activity.audit_description
        when "Add Credits" then activity.audit_description_append = activity.object + " credits"
        when "Credit Use From Challenge" then activity.audit_description_append = activity.subject + " credits"
        else activity.audit_description_append = false

      @$el.html _.template(ActivityItemTemplate, activity)
      @delegateEvents()

      @$('.audit-tooltip').tooltip()

      @

  View