(function() {

  define(['backbone', 'models/company-model'], function(Backbone, CompanyModel) {
    var Collection;
    console.log('company collection loaded');
    return Collection = Backbone.Collection.extend({
      model: CompanyModel
    });
  });

}).call(this);
