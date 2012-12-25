define ['backbone', 'helpers/common', 'models/user-model'], (Backbone, Common, UserModel) ->
  console.log 'user collection loaded'

  Collection = Backbone.Collection.extend

    model: UserModel

    params: {}

    url: ->
      window.baseUrl + 'apiv3/users?' + serialize(this.params)

    parse: (resp, xhr) ->
      if resp.success is true
        return resp.data;
      else if typeof resp.success isnt 'undefined'
        return this.previousAttributes && this.previousAttributes()

      return resp;
