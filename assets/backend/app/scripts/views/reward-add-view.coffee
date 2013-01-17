define [
  'backbone'
  'text!templates/reward-add-template.html'
  'backboneValidationBootstrap'
  'moment'
  ], (Backbone, RewardAddTemplate, BackboneValidationBootstrap, mm) ->

  View = Backbone.View.extend

    id: 'reward-add-view'

    events:
      'submit form.reward-add-form': 'addNewReward'
      'click .box-header': 'minimize'

    initialize: ->
      _.bindAll @
      @model = new window.backend.Models.RewardModel
      Backbone.Validation.bind @

    minimize: (e) ->
      e.preventDefault()
      $target = @$el.find '.box-content'

      if $target.is ':visible'
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down')
      else
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up')

      $target.slideToggle()

      @$("form :input:visible:enabled:first").focus()

    addNewReward: (e) ->
      e.preventDefault()
      newReward =
        name: @$('#reward-add-name').val()
        description: @$('#reward-add-description').val()
        status: @$('#reward-add-status').val()
        redeem_method: @$('#reward-add-redeem-method').val()
        start_timestamp: moment(@$('#reward-add-start-timestamp').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#reward-add-start-timestamp').val()
        end_timestamp: moment(@$('#reward-add-end-timestamp').val(), "MM/DD/YYYY").format("YYYY/MM/DD") if @$('#reward-add-end-timestamp').val()
        redeem:
          amount: @$('#reward-add-amount').val()
          point: @$('#reward-add-point').val()
          once: @$('#reward-add-once').val()
          amount_redeemed: 0
        company_id: 1
        image: '' # todo
        is_points_reward: false
        type: 'redeem'

      # Hardcoded : just validate & save!
      if @model.set newReward
        @model.save null, success: =>
          window.backend.Collections.RewardCollection.add @model.clone()
          @render()

    render: ->
      @$el.html RewardAddTemplate
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View