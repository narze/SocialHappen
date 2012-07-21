define([
  'jquery',
  'underscore',
  'backbone'
], function($, _, Backbone){
  var actionCollection = Backbone.Collection.extend({
    initialize: function(){

    },

    url: window.Passport.BASE_URL + 'apiv3/userActionData'

  });

  return actionCollection;
});
