define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenger'
], function($, _, Backbone, challengerModel){
  var challengersCollection = Backbone.Collection.extend({
    model: challengerModel,
    filter: null,
    last_id: null,

    initialize: function(){
      _.bindAll(this);
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

    loadAll: function(callback) {
      this.filter = null;
      this.fetch({
        success: function(collection, resp){
          if(callback) callback(resp.length);
        }
      });
    },

    url: function() {
      var query = {};
      if(window.Company.companyId) {
        query.company_id = window.Company.companyId;
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

      return window.Company.BASE_URL + 'apiv3/get_challengers/?' + serialize(query);
    }

  });

  return challengersCollection;
});
