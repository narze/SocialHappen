define ['backbone'], (Backbone) ->
  console.log 'reward machine model loaded'
  Model = Backbone.Model.extend

    url: ->
      window.baseUrl + 'apiv3/reward_machines'

    defaults:
      name: null
      description: null
      location: []

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
      name:
        required: true
        msg: 'Name should not be blank'
