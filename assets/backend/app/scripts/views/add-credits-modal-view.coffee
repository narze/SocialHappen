define [
  'backbone'
  'moment'
  'text!templates/add-credits-modal-template.html'
  ], (Backbone, moment, AddCreditsModalTemplate) ->

  View = Backbone.View.extend

    className: 'add-credits-modal-view'

    events:
      'click .cancel': 'cancel'
      'click .add-credits-confirm': 'addCredits'

    initialize: ->
      _.bindAll @

    render: ->
      @$el.html _.template(AddCreditsModalTemplate, @model.toJSON())
      @$('.modal').modal 'show'

      $('#modal').html(@el)

      @$('.add-credits-save').popover
        html: true
        content: =>
          @credits = @$(".modal .credits-to-add").val()
          [
            '<p>'
            @credits + ' credits will be added into ' + @model.get('company_name')
            '</p>'
            '<p>'
            '<button class="btn cancel">Cancel</button>'
            '<button class="btn btn-primary add-credits-confirm" data-dismiss="modal">Confirm</button>'
          ]

      @

    cancel: ->
      @$('.add-credits-save').popover 'hide'

    addCredits: ->
      $.ajax
        dataType: 'json'
        type: 'post'
        data:
          credit: @credits
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