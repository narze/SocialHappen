define ['backbone', 'backboneValidation'], (Backbone, BackboneValidation) ->
  console.log 'reward model loaded'
  Model = Backbone.Model.extend

    url: ->
      window.baseUrl + 'apiv3/reward_list'

    defaults:
      name: null
      description: null
      status: 'published'
      redeem:
        point: 0
        amount: 0
        amount_redeemed: 0
        once: true

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
      image: [
        required: true
        msg: 'Image URL should not be blank'
      ,
        pattern: 'url'
        msg: 'Image URL should be a valid URL'
      ]
      'redeem.amount': [
        required: true
        msg: 'Quantity should not be blank'
      ,
        min: 1
        msg: 'Quantity should be more than 0'
      ]
      'redeem.point': [
        required: true
        msg: 'Point should not be blank'
      ,
        min: 1
        msg: 'Point should be more than 0'
      ]