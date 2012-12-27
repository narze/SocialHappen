define ['backbone', 'helpers/common', 'models/company-model'], (Backbone, Common, CompanyModel) ->
  console.log 'company collection loaded'

  Collection = Backbone.Collection.extend

    model: CompanyModel

    params: {}

    url: ->
      window.baseUrl + 'apiv3/companies?' + serialize(this.params)

    parse: (resp, xhr) ->
      if resp.success is true
        return resp.data;
      else if typeof resp.success isnt 'undefined'
        return this.previousAttributes && this.previousAttributes()

      return resp;
