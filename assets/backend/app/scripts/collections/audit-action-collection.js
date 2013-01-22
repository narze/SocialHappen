(function() {

  define(['backbone', 'backbonePaginator', 'helpers/common', 'models/audit-action-model'], function(Backbone, BackbonePaginator, Common, AuditActionModel) {
    var Collection;
    console.log('audit action collection loaded');
    return Collection = Backbone.Collection.extend({
      model: AuditActionModel,
      params: {},
      url: function() {
        return window.baseUrl + 'apiv3/audit_actions?' + serialize(this.params);
      },
      parse: function(resp, xhr) {
        if (resp.success === true) {
          return resp.data;
        } else if (typeof resp.success !== 'undefined') {
          return this.previousAttributes && this.previousAttributes();
        }
        return resp;
      }
    });
  });

}).call(this);
