define([
  'underscore',
  'backbone'
], function(_, Backbone) {
  var companyUserModel = Backbone.Model.extend({

    idAttribute: 'user_id',

    defaults: {
      user_id: null,
      company_score: 0,
      user_profile: {}
    },

    url: window.Company.BASE_URL + 'apiv3/company_users/'

  });
  return companyUserModel;

});
