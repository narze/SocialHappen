define [
  'backbone'
  'moment'
  'text!templates/device-item-template.html'
  ], (Backbone, moment, DeviceItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'device-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    void: (e) ->
      e.preventDefault()

    render: ->
      @$el.html _.template(DeviceItemTemplate, @model.toJSON())
      @delegateEvents()

      @

  View