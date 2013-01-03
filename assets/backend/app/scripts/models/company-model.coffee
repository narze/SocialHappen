define ['backbone'], (Backbone) ->
  console.log 'company model loaded'
  Model = Backbone.Model.extend
    idAttribute: 'company_id'
    defaults:
      company_id: null