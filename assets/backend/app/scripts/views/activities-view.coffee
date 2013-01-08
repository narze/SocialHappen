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
      paginationCount = @$('.pagination-container').length
      if paginationCount
        if !@subViews.pagination
          @subViews.pagination = []
          for i in [0..paginationCount]
            @subViews.pagination[i] = new PaginationView collection: @collection
        for i in [0..paginationCount]
          @$('.pagination-container:eq(' + i + ')').html @subViews.pagination[i].render().el

      @rendered = true
      @

  View