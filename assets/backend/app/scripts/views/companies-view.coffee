define [
  'backbone'
  'text!templates/companies-template.html'
  'views/pagination-view'
  'views/company-item-view'
  ], (Backbone, CompaniesTemplate, PaginationView, CompanyItemView) ->

  View = Backbone.View.extend

    id: 'companies-view'

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

    addCompany: (model)->
      company = new CompanyItemView model: model
      @subViews['company-' + model.cid] = company
      @$('#company-list').append(company.render().el)

    render: ->
      @$el.html CompaniesTemplate
      @delegateEvents()
      @listCompanies()

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