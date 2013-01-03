(function() {

  define(['backbone'], function(Backbone) {
    var Model;
    console.log('company model loaded');
    return Model = Backbone.Model.extend({
      idAttribute: 'company_id',
      defaults: {
        company_id: null
      }
    });
  });

}).call(this);
