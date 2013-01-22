define ['backbone', 'backbonePaginator', 'helpers/common', 'models/audit-action-model'], (Backbone, BackbonePaginator, Common, AuditActionModel) ->
  console.log 'audit action collection loaded'

  Collection = Backbone.Collection.extend

    model: AuditActionModel

    params: {}

    url: ->
      window.baseUrl + 'apiv3/audit_actions?' + serialize(@params)

    parse: (resp, xhr) ->
      if resp.success is true
        return resp.data;
      else if typeof resp.success isnt 'undefined'
        return @previousAttributes && @previousAttributes()

      return resp;
