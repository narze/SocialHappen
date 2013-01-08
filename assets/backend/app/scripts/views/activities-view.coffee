define [
  'backbone'
  'text!templates/activities-template.html'
  'views/pagination-view'
  'views/activity-item-view'
  ], (Backbone, ActivitiesTemplate, PaginationView, ActivityItemView) ->

  View = Backbone.View.extend

    id: 'activities-view'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listActivities
      @collection.bind 'change', @listActivities
      @collection.fetch()

    listActivities: ->
      @$('#activity-list').empty()
      @collection.each (model) ->
        @addActivity(model)
      , @

    addActivity: (model)->
      activity = new ActivityItemView model: model
      @subViews['activity-' + model.cid] = activity
      @$('#activity-list').append(activity.render().el)



    render: ->
      @$el.html ActivitiesTemplate
      @delegateEvents()
      @listActivities()

      # pagination
      if !@subViews.pagination
        @subViews.pagination = []
        @subViews.pagination[0] = new PaginationView collection: @collection
        @subViews.pagination[1] = new PaginationView collection: @collection
      @$('.pagination-container:eq(0)').html @subViews.pagination[0].render().el
      @$('.pagination-container:eq(1)').html @subViews.pagination[1].render().el

      @rendered = true
      @

  View