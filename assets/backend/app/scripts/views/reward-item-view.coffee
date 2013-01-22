define [
  'backbone'
  'moment'
  'text!templates/reward-item-template.html'
  ], (Backbone, moment, RewardItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'reward-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    void: (e) ->
      e.preventDefault()

    render: ->
      @$el.html _.template(RewardItemTemplate, @model.toJSON())
      @delegateEvents()

      @

  View