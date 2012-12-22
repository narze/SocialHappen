define ['backbone', 'models/company-model'], (Backbone, CompanyModel) ->
  console.log 'company collection loaded'
  Collection = Backbone.Collection.extend
    model: CompanyModel