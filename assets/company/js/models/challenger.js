define([
  'underscore',
  'backbone',
  'sandbox'
], function(_, Backbone, sandbox) {
  var couponModel = Backbone.Model.extend({

    idAttribute: 'hash',

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

    url: window.Company.BASE_URL + 'apiv3/get_challengers/',


    //parse saveCoupon's response
    //{success: [success?], 'data': [data]}
    parse: function(resp, xhr) {
      if(resp.success === true) {
        return resp.data;
      } else if(typeof resp.success !== 'undefined') {
        return this.previousAttributes();
      }

      //if resp.success is undefined, resp itself is data
      return resp;
    }

  });
  return couponModel;

});
