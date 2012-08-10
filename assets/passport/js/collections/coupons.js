define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'sandbox'
], function($, _, Backbone, couponModel, sandbox){
  var couponsCollection = Backbone.Collection.extend({
    model: couponModel,
    filter: null,
    last_id: null,

    initialize: function(){
      _.bindAll(this);
      this.isFetched = false;
      this.bind('reset', this.onReset, this);
    },

    onReset: function() {
        this.isFetched = true;
    },

    loadMore: function(callback){
      if(this.models.length === 0){
        this.last_id = null;
      }else{
        this.last_id = this.last().id;
      }

      this.fetch({
        add: true,
        success: function(collection, resp){
          callback(resp.length);
        }
      });
    },

    url: function() {
      var query = {};
      if(sandbox.userId) {
        query.user_id = sandbox.userId;
      }
      if(this.last_id) {
        query.last_id = this.last_id;
      }
      if(this.filter) {
        query.filter = this.filter;
      }

      var serialize = function(obj, prefix) {
        var str = [];
        for(var p in obj) {
          var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
          str.push(typeof v === "object" ?
            serialize(v, k) :
            encodeURIComponent(k) + "=" + encodeURIComponent(v));
        }
        return str.join("&");
      };

      return window.Passport.BASE_URL + 'apiv3/coupons/?' + serialize(query);
    }

  });

  return couponsCollection;
});
