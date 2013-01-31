(function() {

  define(['backbone'], function(Backbone) {
    var Model;
    console.log('branch model loaded');
    return Model = Backbone.Model.extend({
      idAttribute: '_id'
    });
  });

}).call(this);
