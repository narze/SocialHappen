define [
  'backbone'
  'text!templates/activities-template.html'
  'views/activity-item-view'
  ], (Backbone, ActivitiesTemplate, ActivityItemView) ->

  View = Backbone.View.extend

    id: 'activities-view'

    events:
      # pagination
      'click a.servernext': 'nextResultPage'
      'click a.serverprevious': 'previousResultPage'
      # 'click a.orderUpdate': 'updateSortBy'
      'click a.serverlast': 'gotoLast'
      'click a.page': 'gotoPage'
      'click a.serverfirst': 'gotoFirst'
      # 'click a.serverpage': 'gotoPage'


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

      @pagination()

    addActivity: (model)->
      activity = new ActivityItemView model: model
      @subViews['activity-' + model.cid] = activity
      @$('#activity-list').append(activity.render().el)

    pagination: ->
      @$('.activities-pagination').html \
        _.template \
          @$('#activities-pagination-template').html(),
          @collection.info()


    nextResultPage: (e) ->
      e.preventDefault()
      @collection.requestNextPage()

    previousResultPage: (e) ->
      e.preventDefault()
      @collection.requestPreviousPage()

    gotoFirst: (e) ->
      e.preventDefault()
      @collection.goTo(@collection.information.firstPage)

    gotoLast: (e) ->
      e.preventDefault()
      @collection.goTo(@collection.information.lastPage)

    gotoPage: (e) ->
      e.preventDefault()
      page = $(e.target).text()
      @collection.goTo(page)

    render: ->
      @$el.html ActivitiesTemplate
      @listActivities()
      @rendered = true
      @

  View