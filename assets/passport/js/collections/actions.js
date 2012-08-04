define([
  'jquery',
  'underscore',
  'backbone'
], function($, _, Backbone){
  var actionCollection = Backbone.Collection.extend({
  	userId: null,
    initialize: function(){

    },

    url: function(){
    	if(this.userId){
    		return window.Passport.BASE_URL + 'apiv3/userActionData/' + this.userId;
    	}else{
    		return window.Passport.BASE_URL + 'apiv3/userActionData';
    	}
    }

  });

  return actionCollection;
});
