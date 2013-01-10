define [
  'backbone'
  'text!templates/activities-template.html'
  'views/activities-filter-view'
  'views/pagination-view'
  'views/activity-item-view'
  ], (Backbone, ActivitiesTemplate, ActivitiesFilterView, PaginationView, ActivityItemView) ->

  View = Backbone.View.extend

    id: 'activities-view'

    events:
      'click .sort-date': 'sort'

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

    sort: (e) ->
      e.preventDefault()

      $target = $(e.currentTarget)

      if $target.hasClass 'sort-asc'
        $target.removeClass 'sort-asc'
        $target.addClass 'sort-desc'
        $target.removeClass('icon-chevron-up').addClass('icon-chevron-down')
        @collection.order = '-'
      else
        $target.removeClass 'sort-desc'
        $target.addClass 'sort-asc'
        $target.removeClass('icon-chevron-down').addClass('icon-chevron-up')
        @collection.order = '+'

      @collection.sort = 'timestamp'

      @collection.fetch()

    render: ->
      @$el.html ActivitiesTemplate
      @delegateEvents()
      @listActivities()

      # filter
      if !@subViews.filter
        @subViews.filter = new ActivitiesFilterView collection: @collection

      @$('.activities-filter-container').html @subViews.filter.render().el

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