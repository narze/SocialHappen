define [
  'backbone'
  'moment'
  'text!templates/company-item-template.html'
  'text!templates/add-credits-modal.html'
  ], (Backbone, moment, CompanyItemTemplate, AddCreditsModalTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'company-item'

    events:
      'click .add-credits': 'addCreditsModal'
      'click .add-credits-save': 'addCredits'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    render: ->
      @$el.html _.template(CompanyItemTemplate, @model.toJSON())
      @

    addCreditsModal: ->
      console.log('addCreditsModal');
      # render model
      modal = @$('.add-credits-modal')
      modal.html _.template(AddCreditsModalTemplate, @model.toJSON())
      modal.modal()

    addCredits: ->
      credits = @$('.add-credits-modal .credits-to-add').val()
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
            @$('.add-credits-modal').modal('hide')
          else
            alert resp.data

    updateCredits: (credits) ->
      @model.set 'credits', credits

  View