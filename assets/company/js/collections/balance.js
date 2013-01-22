define([
  'jquery',
  'underscore',
  'backbone',
  'models/balance'
], function($, _, Backbone, balanceModel){
  var balanceCollection = Backbone.Collection.extend({
    model: balanceModel,
    filter: null,
    last_id: null,

    initialize: function(){
      _.bindAll(this);
    },

    loadMore: function(callback){
      if(this.models.length === 0){
        this.last_id = null;
      } else {
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
          if(callback) { callback(resp.length); }
        }
      });
    }
  });

  return balanceCollection;
});
