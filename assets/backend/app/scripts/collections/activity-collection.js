(function() {

  define(['backbone', 'helpers/common', 'models/activity-model'], function(Backbone, Common, ActivityModel) {
    var Collection;
    console.log('activity collection loaded');
    return Collection = Backbone.Collection.extend({
      model: ActivityModel,
      params: {},
      url: function() {
        return window.baseUrl + 'apiv3/activities?' + serialize(this.params);
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
