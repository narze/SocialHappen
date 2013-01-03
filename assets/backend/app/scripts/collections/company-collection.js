(function() {

  define(['backbone', 'helpers/common', 'models/company-model'], function(Backbone, Common, CompanyModel) {
    var Collection;
    console.log('company collection loaded');
    return Collection = Backbone.Collection.extend({
      model: CompanyModel,
      params: {},
      url: function() {
        return window.baseUrl + 'apiv3/companies?' + serialize(this.params);
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
