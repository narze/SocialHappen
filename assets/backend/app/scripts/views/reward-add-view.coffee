define [
  'backbone'
  'text!templates/reward-add-template.html'
  'backboneValidationBootstrap'
  'moment'
  'jqueryForm'
  ], (Backbone, RewardAddTemplate, BackboneValidationBootstrap, mm, jqform) ->

  View = Backbone.View.extend

    id: 'reward-add-view'

    events:
      'submit form.reward-add-form': 'addNewReward'
      'click .box-header': 'minimize'
      'click a.upload-image': 'uploadImage'

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
        image: @$('#reward-add-image').val()
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
        is_points_reward: false
        type: 'redeem'

      # Hardcoded : just validate & save!
      if @model.set newReward
        @model.save null, success: =>
          window.backend.Collections.RewardCollection.add @model.clone()
          @render()

    uploadImage: (e) ->
      e.preventDefault()

      # Move the file input and clone back
      @$('.reward-add-form .reward-add-image:first').after @$('.reward-add-form .reward-add-image:first').clone()
      @$('form.upload-image .file-input').html(@$('.reward-add-form .reward-add-image:first'))

      @$('form.upload-image').ajaxSubmit
        beforeSubmit: (a, f, o) ->
          o.dataType = 'json';
        success: (resp) =>
          if resp.success
            imageUrl = resp.data;
            @$('#reward-add-image').val imageUrl
          else
            alert 'There is an error uploading the image'
            # console.log resp

    render: ->
      @$el.html _.template RewardAddTemplate, {}
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View