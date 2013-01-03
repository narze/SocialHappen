define [
  'backbone'
  'text!templates/companies-template.html'
  'views/company-item-view'
  ], (Backbone, CompaniesTemplate, CompanyItemView) ->

  View = Backbone.View.extend

    id: 'companies-view'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listCompanies
      @collection.fetch()

    listCompanies: ->
      @collection.each (model) ->
        @addCompany(model)
      , @

    addCompany: (model)->
      company = new CompanyItemView model: model
      @subViews['company-' + model.cid] = company
      @$('#company-list').append(company.render().el)

    render: ->
      @$el.html CompaniesTemplate
      @listCompanies()
      @rendered = true
      @

  View