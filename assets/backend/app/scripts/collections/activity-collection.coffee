define ['backbone', 'helpers/common', 'models/activity-model'], (Backbone, Common, ActivityModel) ->
  console.log 'activity collection loaded'

  Collection = Backbone.Collection.extend

    model: ActivityModel

    params: {}

    url: ->
      window.baseUrl + 'apiv3/activities?' + serialize(this.params)

    parse: (resp, xhr) ->
      if resp.success is true
        return resp.data;
      else if typeof resp.success isnt 'undefined'
        return this.previousAttributes && this.previousAttributes()

      return resp;
