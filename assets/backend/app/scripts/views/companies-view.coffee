define [
  'backbone'
  'text!templates/companies-template.html'
  'views/companies-pagination-view'
  'views/company-item-view'
  ], (Backbone, CompaniesTemplate, CompaniesPaginationView, CompanyItemView) ->

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
        @subViews.pagination[0] = new CompaniesPaginationView collection: @collection
        @subViews.pagination[1] = new CompaniesPaginationView collection: @collection
      @$('.companies-pagination:first').html @subViews.pagination[0].render().el
      @$('.companies-pagination:last').html @subViews.pagination[1].render().el

      @rendered = true
      @

  View