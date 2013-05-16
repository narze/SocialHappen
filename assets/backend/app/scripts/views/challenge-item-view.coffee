define [
  'backbone'
  'moment'
  'text!templates/challenge-item-template.html'
  ], (Backbone, moment, ChallengeItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'challenge-item'

    events:
      'click .view': 'viewItem'
      'click .edit': 'editItem'
      'click .delete': 'deleteItem'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    void: (e) ->
      e.preventDefault()

    render: ->
      challengeItem = @model.toJSON()
      challengeItem.branch_sonar_data = challengeItem.branch_sonar_data || []

      @$el.html _.template(ChallengeItemTemplate, challengeItem)
      @delegateEvents()

      @

    viewItem: (e) ->
      e.preventDefault()

    editItem: (e) ->
      e.preventDefault()

    deleteItem: (e) ->
      e.preventDefault()

  View