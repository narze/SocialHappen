define([
  'underscore',
  'backbone'
], function(_, Backbone) {
  var balanceModel = Backbone.Model.extend({

    idAttribute: '_id',

    defaults: {
      reward_item_id: null,
      user_id: null,
      company_id: null,
      timestamp: null,
      confirmed: null,
      confirmed_timestamp: null,
      confirmed_by_id: null,
      challenge_id: null
    },

    initialize: function(){

    },

    urlRoot: window.Company.BASE_URL + 'apiv3/company_balance/',

    parse: function(resp, xhr) {
      //get mongo nesting id
      var id = resp[this.idAttribute].$id;
      resp[this.idAttribute] = id;

      if(resp.success === true) {
        return resp.data;
      } else if(typeof resp.success !== 'undefined') {
        return this.previousAttributes();
      }

      return resp;
    }

  });
  return balanceModel;

});
