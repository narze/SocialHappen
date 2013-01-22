define [
  'backbone'
  'text!templates/challenges-template.html'
  'views/challenges-filter-view'
  'views/pagination-view'
  'views/challenge-item-view'
  ], (Backbone, ChallengesTemplate, ChallengesFilterView, PaginationView, ChallengeItemView) ->

  View = Backbone.View.extend

    id: 'challenges-view'

    events:
      'click .sort-name': 'sort'
      'click .sort-start-date': 'sort'
      'click .sort-end-date': 'sort'
      'click .sort-sonar-data': 'sort'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listChallenges
      @collection.bind 'change', @listChallenges
      @collection.fetch()

    listChallenges: ->
      @$('#challenge-list').empty()
      @collection.each (model) ->
        @addChallenge(model)
      , @

    addChallenge: (model)->
      challenge = new ChallengeItemView model: model
      @subViews['challenge-' + model.cid] = challenge
      @$('#challenge-list').append(challenge.render().el)

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

      if $target.hasClass 'sort-name'
        @collection.sort = 'name'
      else if $target.hasClass 'sort-start-date'
        @collection.sort = 'start_date'
      else if $target.hasClass 'sort-end-date'
        @collection.sort = 'end_date'
      else if $target.hasClass 'sort-sonar-data'
        @collection.sort = 'sonar_data'

      @collection.fetch()

    render: ->
      @$el.html ChallengesTemplate
      @delegateEvents()
      @listChallenges()

      # filter
      if !@subViews.filter
        @subViews.filter = new ChallengesFilterView collection: @collection

      @$('.challenges-filter-container').html @subViews.filter.render().el

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