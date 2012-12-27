define [
  'backbone'
  'moment'
  'text!templates/company-item-template.html'
  ], (Backbone, moment, CompanyItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'company-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    render: ->
      @$el.html _.template(CompanyItemTemplate, @model.toJSON())
      @

  View