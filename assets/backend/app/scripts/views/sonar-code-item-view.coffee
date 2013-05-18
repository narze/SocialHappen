define [
  'backbone'
  'moment'
  'text!templates/sonar-code-item-template.html'
  ], (Backbone, moment, SonarCodeItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'sonar-code-item'

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
      @$el.html _.template(SonarCodeItemTemplate, @model.toJSON())
      @delegateEvents()

      @

    viewItem: (e) ->
      e.preventDefault()

    editItem: (e) ->
      e.preventDefault()

    deleteItem: (e) ->
      e.preventDefault()

  View