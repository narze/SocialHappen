define([
  'underscore',
  'backbone'
], function(_, Backbone) {
  var rewardModel = Backbone.Model.extend({

    idAttribute: 'company_id'

  });
  return rewardModel;

});
