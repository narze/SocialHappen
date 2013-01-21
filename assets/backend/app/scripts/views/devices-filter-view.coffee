define [
  'backbone'
  'text!templates/devices-filter-template.html'
  'moment'
  ], (Backbone, DevicesFilterTemplate, mm) ->

  View = Backbone.View.extend

    id: 'devices-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.devices-filter': 'filter'

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
        challenge: @$('#filter-challenge').val()
        data: @$('#filter-data').val()

      @collection.fetch()

    render: ->
      @$el.html DevicesFilterTemplate
      @delegateEvents()

      @rendered = true
      @

  View