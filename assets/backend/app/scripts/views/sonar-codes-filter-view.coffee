define [
  'backbone'
  'text!templates/sonar-codes-filter-template.html'
  'moment'
  ], (Backbone, SonarCodesFilterTemplate, mm) ->

  View = Backbone.View.extend

    id: 'sonar-codes-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.sonar-codes-filter': 'filter'

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
        type: @$('#filter-type').val()
        code: @$('#filter-code').val()

      @collection.fetch()

    render: ->
      @$el.html SonarCodesFilterTemplate
      @delegateEvents()

      @rendered = true
      @

  View