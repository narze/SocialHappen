define([
  'jquery',
  'underscore',
  'backbone',
  'models/company-user'
], function($, _, Backbone, companyUserModel){
  var companyUsersCollection = Backbone.Collection.extend({
    model: companyUserModel,

    initialize: function(){
      _.bindAll(this);
    },

    parse: function(resp, xhr) {
      if(resp.success === true) {
        return resp.data;
      } else if(typeof resp.success !== 'undefined') {
        return []
      }

      //if resp.success is undefined, resp itself is data
      return resp;
    }
  });

  return companyUsersCollection;
});
