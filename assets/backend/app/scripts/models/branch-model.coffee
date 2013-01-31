define ['backbone'], (Backbone) ->
  console.log 'branch model loaded'
  Model = Backbone.Model.extend

    idAttribute: '_id'