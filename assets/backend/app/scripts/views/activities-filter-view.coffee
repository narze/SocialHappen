define [
  'backbone'
  'text!templates/activities-filter-template.html'
  'collections/audit-action-collection'
  'moment'
  'jqueryPlugins/jquery.chosen.min'
  ], (Backbone, ActivitiesFilterTemplate, AuditActionCollection, mm, chosen) ->

  View = Backbone.View.extend

    id: 'activities-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.activities-filter': 'filter'
      'click .filter-action-preset': 'actionPreset'
      'reset form.activities-filter': 'reset'

    initialize: ->
      _.bindAll @
      @auditActionCollection = new AuditActionCollection()
      @auditActionCollection.bind 'reset', @prepareCollection
      @auditActionCollection.fetch()

    reset: ->
      @$('#filter-action').next().find('li.search-choice .search-choice-close').click()

    prepareCollection: ->
      @$('#filter-action').empty()

      @auditActionCollection.each (model) =>
        @$('#filter-action').append('<option>' + model.get('description') + '</option>')

      @$('#filter-action').trigger "liszt:updated"

    minimize: (e) ->
      e.preventDefault()
      $target = @$el.find '.box-content'

      if $target.is ':visible'
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down')
      else
        @$('[data-rel="chosen"],[rel="chosen"]').chosen()
        @$('#filter-action').next().css width: '220px'
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

    actionPreset: (e) ->
      e.preventDefault()

      # Get actions from data-filter
      actions = @$(e.currentTarget).data('filter').split(',')
      @$('#filter-action').val(actions)

      @$('#filter-action').trigger "liszt:updated"

    render: ->
      @$el.html ActivitiesFilterTemplate
      @prepareCollection()
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View