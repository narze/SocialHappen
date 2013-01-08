define [
  'backbone'
  'text!templates/companies-template.html'
  'views/company-item-view'
  ], (Backbone, CompaniesTemplate, CompanyItemView) ->

  View = Backbone.View.extend

    id: 'companies-view'

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
      @collection.bind 'reset', @listCompanies
      @collection.bind 'change', @listCompanies
      @collection.fetch()

    listCompanies: ->
      @$('#company-list').empty()
      @collection.each (model) ->
        @addCompany(model)
      , @

      @pagination()

    addCompany: (model)->
      company = new CompanyItemView model: model
      @subViews['company-' + model.cid] = company
      @$('#company-list').append(company.render().el)

    pagination: ->
      @$('.companies-pagination').html \
        _.template \
          @$('#companies-pagination-template').html(),
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
      @$el.html CompaniesTemplate
      @delegateEvents()
      @listCompanies()
      @rendered = true
      @

  View