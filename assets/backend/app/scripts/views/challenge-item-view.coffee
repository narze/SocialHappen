define [
  'backbone'
  'moment'
  'text!templates/challenge-item-template.html'
  ], (Backbone, moment, ChallengeItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'challenge-item'

    events:
      'click .audit-tooltip': 'void'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    void: (e) ->
      e.preventDefault()

    render: ->
      @$el.html _.template(ChallengeItemTemplate, @model.toJSON())
      @delegateEvents()

      @$('.audit-tooltip').tooltip()

      @

  View