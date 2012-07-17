define([
  'jquery',
  'underscore',
  'backbone',
  'models/card'
], function($, _, Backbone, cardModel){
  var cardsCollection = Backbone.Collection.extend({
    model: cardModel,
    filter: null,
    last_id: null,

    initialize: function(){
      _.bindAll(this);
    },

    url: window.Passport.BASE_URL + 'apiv3/getMyCards',

    parse: function(res) {
      if(!res.success) {
        res.data = [];
      }
      return res.data;
    }
  });

  return cardsCollection;
});
