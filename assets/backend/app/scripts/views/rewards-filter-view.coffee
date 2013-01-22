define [
  'backbone'
  'text!templates/rewards-filter-template.html'
  'moment'
  ], (Backbone, RewardsFilterTemplate, mm) ->

  View = Backbone.View.extend

    id: 'rewards-filter-view'

    events:
      'click .box-header': 'minimize'
      'submit form.rewards-filter': 'filter'

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
        point_from: @$('#filter-point-required-from').val()
        point_to: @$('#filter-point-required-to').val()
        amount_from: @$('#filter-amount-from').val()
        amount_to: @$('#filter-amount-to').val()
        amount_redeemed_from: @$('#filter-amount-redeemed-from').val()
        amount_redeemed_to: @$('#filter-amount-redeemed-to').val()
        once: @$('#filter-can-play-once').val()

      @collection.fetch()

    render: ->
      @$el.html RewardsFilterTemplate
      @delegateEvents()

      @rendered = true
      @

  View