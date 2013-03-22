define [
  'backbone'
  'text!templates/reward-machine-add-template.html'
  'backboneValidationBootstrap'
  'moment'
  ], (Backbone, RewardMachineAddTemplate, BackboneValidationBootstrap, mm, jqform, chosen) ->

  View = Backbone.View.extend

    id: 'reward-machine-add-view'

    events:
      'submit form.reward-machine-add-form': 'addNewRewardMachine'
      'click .box-header': 'minimize'

    initialize: ->
      _.bindAll @
      @model = new window.backend.Models.RewardMachineModel
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

    addNewRewardMachine: (e) ->
      e.preventDefault()

      newRewardMachine =
        name: @$('#reward-machine-add-name').val()
        description: @$('#reward-machine-add-description').val()
        location: [@$('#reward-machine-add-location-longitude').val(), @$('#reward-machine-add-location-latitude').val()]

      console.log(newRewardMachine);
      # Hardcoded : just validate & save!
      if @model.set newRewardMachine
        @model.save null, success: =>
          window.backend.Collections.RewardMachineCollection.add @model.clone()
          @render()

    render: ->
      @$el.html _.template RewardMachineAddTemplate, {}
      @delegateEvents()

      @rendered = true
      @

  View