(function() {

  define(['backbone', 'helpers/common', 'models/user-model'], function(Backbone, Common, UserModel) {
    var Collection;
    console.log('user collection loaded');
    return Collection = Backbone.Collection.extend({
      model: UserModel,
      params: {},
      url: function() {
        return window.baseUrl + 'apiv3/users?' + serialize(this.params);
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
