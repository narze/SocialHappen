define [
  'backbone'
  'text!templates/activities-template.html'
  'views/activity-item-view'
  ], (Backbone, ActivitiesTemplate, ActivityItemView) ->

  View = Backbone.View.extend

    id: 'activities-view'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listActivities
      @collection.fetch()

    listActivities: ->
      @collection.each (model) ->
        @addActivity(model)
      , @

    addActivity: (model)->
      activity = new ActivityItemView model: model
      @subViews['activity-' + model.cid] = activity
      @$('#activity-list').append(activity.render().el)

    render: ->
      @$el.html ActivitiesTemplate
      @listActivities()
      @rendered = true
      @

  View