define ['backbone', 'backboneValidation'], (Backbone, BackboneValidation) ->
  console.log 'device model loaded'
  Model = Backbone.Model.extend

    url: ->
      window.baseUrl + 'apiv3/devices'

    defaults:
      id: null
      title: null
      data: null
      company: null
      branch: null

    parse: (resp, xhr) ->
      if resp.success is true
        @set resp.data
        return resp.data
      else if resp.success is false
        if @isNew()
          alert resp.data
          return @collection.remove @
        return @previousAttributes && @previousAttributes()

      resp

    validation:
      id:
        required: true
        msg: 'ID should not be blank'
      title:
        required: true
        msg: 'Title should not be blank'
      data:
        required: true
        msg: 'Data should not be blank'
      company:
        required: true
        msg: 'Please choose a company'
      branch:
        required: true
        msg: 'Please choose a branch'