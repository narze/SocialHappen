define [
  'backbone'
  'text!templates/sonar-codes-template.html'
  'views/sonar-codes-filter-view'
  'views/pagination-view'
  'views/sonar-code-item-view'
  'views/sonar-code-add-view'
  ], (Backbone, SonarCodesTemplate, SonarCodesFilterView, PaginationView, SonarCodeItemView, SonarCodeAddView) ->

  View = Backbone.View.extend

    id: 'sonar-codes-view'

    events:
      'click .sort-name': 'sort'
      'click .sort-data': 'sort'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listSonarCodes
      @collection.bind 'add', @listSonarCodes
      @collection.bind 'remove', @listSonarCodes
      @collection.fetch()

    listSonarCodes: ->
      @$('#sonar-code-list').empty()
      @collection.each (model) ->
        @addSonarCode(model)
      , @

    addSonarCode: (model)->
      sonarCode = new SonarCodeItemView model: model
      @subViews['sonar-code-' + model.cid] = sonarCode
      @$('#sonar-code-list').append(sonarCode.render().el)

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
      else if $target.hasClass 'sort-data'
        @collection.sort = 'data'

      @collection.fetch()

    render: ->
      @$el.html SonarCodesTemplate
      @delegateEvents()
      @listSonarCodes()

      # filter
      if !@subViews.filter
        @subViews.filter = new SonarCodesFilterView collection: @collection

      @$('.sonar-codes-filter-container').html @subViews.filter.render().el

      # pagination
      paginationCount = @$('.pagination-container').length
      if paginationCount
        if !@subViews.pagination
          @subViews.pagination = []
          for i in [0..paginationCount]
            @subViews.pagination[i] = new PaginationView collection: @collection
        for i in [0..paginationCount]
          @$('.pagination-container:eq(' + i + ')').html @subViews.pagination[i].render().el

      # sonar-code add form
      if !@subViews['sonar-code-add']
        @subViews['sonar-code-add'] = new SonarCodeAddView model: new @collection.model

      @$('#sonar-code-add-container').html @subViews['sonar-code-add'].render().el

      @rendered = true
      @

  View