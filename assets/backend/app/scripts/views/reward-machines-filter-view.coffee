define [
  'backbone'
  'text!templates/reward-machines-filter-template.html'
  'moment'
  ], (Backbone, RewardMachinesFilterTemplate, mm) ->

  View = Backbone.View.extend

    id: 'reward-machines-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.reward-machines-filter': 'filter'

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
        id: @$('#filter-id').val()
        name: @$('#filter-name').val()
        description: @$('#filter-description').val()

      @collection.fetch()

    render: ->
      @$el.html RewardMachinesFilterTemplate
      @delegateEvents()

      @rendered = true
      @

  View