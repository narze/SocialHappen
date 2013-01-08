define [
  'backbone'
  'text!templates/companies-pagination-template.html'
  ], (Backbone, CompaniesPaginationTemplate) ->

  View = Backbone.View.extend

    class: 'companies-pagination-view'

    events:
      # pagination
      'click a.servernext': 'nextResultPage'
      'click a.serverprevious': 'previousResultPage'
      # 'click a.orderUpdate': 'updateSortBy'
      'click a.serverlast': 'gotoLast'
      'click a.page': 'gotoPage'
      'click a.serverfirst': 'gotoFirst'
      # 'click a.serverpage': 'gotoPage'

    template: _.template CompaniesPaginationTemplate

    initialize: ->
      _.bindAll @
      @collection.bind 'reset', @render
      @collection.bind 'change', @render

    pagination: ->
      @$el.html @template @collection.info()

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
      @$el.html @template @collection.info()
      @delegateEvents()
      @rendered = true
      @

  View