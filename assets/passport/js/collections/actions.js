define([
  'jquery',
  'underscore',
  'backbone',
  'sandbox'
], function($, _, Backbone, sandbox){
  var actionCollection = Backbone.Collection.extend({
    initialize: function(){

    },

    url: function(){
      if(sandbox.userId){
        return window.Passport.BASE_URL + 'apiv3/userActionData/' + sandbox.userId;
      }else{
        return window.Passport.BASE_URL + 'apiv3/userActionData';
      }
    }
  });

  return actionCollection;
});
