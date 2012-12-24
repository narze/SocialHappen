define [
  'backbone'
  'text!templates/activities-template.html'
  ], (Backbone, ActivitiesTemplate) ->

  View = Backbone.View.extend
    id: 'activities-view'
    initialize: ->
    render: ->
      @$el.html ActivitiesTemplate
      @rendered = true
      @

  View