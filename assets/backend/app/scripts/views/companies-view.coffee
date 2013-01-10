define [
  'backbone'
  'text!templates/companies-template.html'
  'views/companies-filter-view'
  'views/pagination-view'
  'views/company-item-view'
  ], (Backbone, CompaniesTemplate, CompaniesFilterView, PaginationView, CompanyItemView) ->

  View = Backbone.View.extend

    id: 'companies-view'

    events:
      'click .sort-name': 'sort'
      'click .sort-created-at': 'sort'
      'click .sort-credits': 'sort'

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
        @collection.sort = 'company_name'
      else if $target.hasClass 'sort-created-at'
        @collection.sort = 'company_register_date'
      else if $target.hasClass 'sort-credits'
        @collection.sort = 'credits'

      @collection.fetch()

    render: ->
      @$el.html CompaniesTemplate
      @delegateEvents()
      @listCompanies()

      # filter
      if !@subViews.filter
        @subViews.filter = new CompaniesFilterView collection: @collection

      @$('.companies-filter-container').html @subViews.filter.render().el

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