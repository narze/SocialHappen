define [
  'backbone'
  'moment'
  'text!templates/company-item-template.html'
  'views/add-credits-modal-view'
  ], (Backbone, moment, CompanyItemTemplate, AddCreditsModalView) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'company-item'

    events:
      'click .add-credits': 'showAddCreditsModal'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    render: ->
      @$el.html _.template(CompanyItemTemplate, @model.toJSON())
      @

    showAddCreditsModal: ->
      console.log('showAddCreditsModal');
      addCreditsModalView = new AddCreditsModalView model: @model
      addCreditsModalView.render()
  View