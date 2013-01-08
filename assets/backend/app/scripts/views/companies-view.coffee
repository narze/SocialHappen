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