define [
  'backbone'
  'text!templates/companies-filter-template.html'
  'moment',
  'jqueryPlugins/jquery.chosen.min'
  ], (Backbone, CompaniesFilterTemplate, mm, chosen) ->

  View = Backbone.View.extend

    id: 'companies-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.companies-filter': 'filter'

    initialize: ->
      _.bindAll @

    minimize: (e) ->
      e.preventDefault()
      $target = @$el.find '.box-content'

      if $target.is ':visible'
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down')
      else
        $('[data-rel="chosen"],[rel="chosen"]').chosen()
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up')

      $target.slideToggle()

    filter: (e) ->
      e.preventDefault()
      @collection.filter =
        name: @$('#filter-name').val()
        created_at_from: moment(@$('#filter-created-at-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-created-at-from').val()
        created_at_to: moment(@$('#filter-created-at-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-created-at-to').val()
        credits: @$('#filter-credits').val()

      @collection.fetch()

    render: ->
      @$el.html CompaniesFilterTemplate
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View