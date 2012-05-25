define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/player/challenge-list.html',
  'views/player/challenge-item'
], function($, _, Backbone, challengeListTemplate, ChallengeItemView){
  var ChallengeList = Backbone.View.extend({

    el: '#challenge-criteria-list',

    challengeListTemplate: _.template(challengeListTemplate),
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      // $(this.el).html(this.challengeListTemplate({
      //   list: this.collection
      // }));
      
      return this;
    }

  });
  return ChallengeList;
});
