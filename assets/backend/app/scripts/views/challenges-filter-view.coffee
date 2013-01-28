define [
  'backbone'
  'text!templates/challenges-filter-template.html'
  'moment'
  ], (Backbone, ChallengesFilterTemplate, mm) ->

  View = Backbone.View.extend

    id: 'challenges-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.challenges-filter': 'filter'

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
        name: @$('#filter-name').val()
        company: @$('#filter-company').val()
        sonar_data: @$('#filter-sonar-data').val()
        start_date_from: moment(@$('#filter-start-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-start-date-from').val()
        start_date_to: moment(@$('#filter-start-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-start-date-to').val()
        end_date_from: moment(@$('#filter-end-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-end-date-from').val()
        end_date_to: moment(@$('#filter-end-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-end-date-to').val()

      @collection.fetch()

    render: ->
      @$el.html ChallengesFilterTemplate
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View