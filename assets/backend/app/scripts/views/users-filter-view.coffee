define [
  'backbone'
  'text!templates/users-filter-template.html'
  'moment'
  'jqueryPlugins/jquery.chosen.min'
  ], (Backbone, UsersFilterTemplate, mm, chosen) ->

  View = Backbone.View.extend

    id: 'users-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.users-filter': 'filter'
      'reset form.users-filter': 'reset'

    initialize: ->
      _.bindAll @

    reset: ->
      @$('#filter-platforms').next().find('li.search-choice .search-choice-close').click()

    minimize: (e) ->
      e.preventDefault()
      $target = @$el.find '.box-content'

      if $target.is ':visible'
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down')
      else
        @$('[data-rel="chosen"],[rel="chosen"]').chosen()
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up')

      $target.slideToggle()

    filter: (e) ->
      e.preventDefault()
      @collection.filter =
        first_name: @$('#filter-first-name').val()
        last_name: @$('#filter-last-name').val()
        signup_date_from: moment(@$('#filter-signup-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-signup-date-from').val()
        signup_date_to: moment(@$('#filter-signup-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-signup-date-to').val()
        last_seen_from: moment(@$('#filter-last-seen-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-last-seen-from').val()
        last_seen_to: moment(@$('#filter-last-seen-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#filter-last-seen-to').val()
        points: @$('#filter-points').val()
        platforms: @$('#filter-platforms').val()

      @collection.fetch()

    render: ->
      @$el.html UsersFilterTemplate
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View