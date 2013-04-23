define [
  'backbone'
  'moment'
  'text!templates/reward-machine-item-template.html'
  ], (Backbone, moment, RewardMachineItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'reward-machine-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    void: (e) ->
      e.preventDefault()

    render: ->
      @$el.html _.template(RewardMachineItemTemplate, @model.toJSON())
      @delegateEvents()

      @

  View