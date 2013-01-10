define [
  'backbone'
  'text!templates/activities-filter-template.html'
  'moment',
  ], (Backbone, ActivitiesFilterTemplate, mm) ->

  View = Backbone.View.extend

    id: 'activities-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.activities-filter': 'filter'

    initialize: ->
      _.bindAll @

    minimize: (e) ->
      e.preventDefault()
      $target = @$el.find '.box-content'

      if $target.is ':visible'
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down')
      else
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up')

      $target.slideToggle()

    filter: (e) ->
      e.preventDefault()
      @collection.filter =
        first_name: @$('#filter-first-name').val()
        last_name: @$('#filter-last-name').val()
        action: @$('#filter-action').val()
        date_from: moment(@$('#filter-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-date-from').val()
        date_to: moment(@$('#filter-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-date-to').val()
        company: @$('#filter-company').val()
        branch: @$('#filter-branch').val()
        challenge: @$('#filter-challenge').val()

      @collection.fetch()

    render: ->
      @$el.html ActivitiesFilterTemplate
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View