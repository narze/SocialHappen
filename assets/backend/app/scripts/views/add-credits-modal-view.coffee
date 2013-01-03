define [
  'backbone'
  'moment'
  'text!templates/add-credits-modal-template.html'
  ], (Backbone, moment, AddCreditsModalTemplate) ->

  View = Backbone.View.extend

    className: 'add-credits-modal-view'

    events:
      'click .add-credits-save': 'addCredits'

    initialize: ->
      _.bindAll @

    render: ->
      @$el.html _.template(AddCreditsModalTemplate, @model.toJSON())
      @$('.modal').modal 'show'

      $('#modal').html(@el)
      @

    addCredits: ->
      credits = @$('.modal .credits-to-add').val()
      $.ajax
        dataType: 'json'
        type: 'post'
        data:
          credit: credits
          company_id: @model.id
        url: window.baseUrl + 'apiv3/credit_add'
        success: (resp) =>
          if resp.success
            @updateCredits resp.data.credits
            @$('.modal').modal 'hide'
          else
            alert resp.data

    updateCredits: (credits) ->
      @model.set 'credits', credits

  View