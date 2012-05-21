define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/challenge-item.html'
], function($, _, Backbone, challengeItemTemplate){
  var ChallengeItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    challengeItemTemplate: _.template(challengeItemTemplate),    
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      var data = this.model.toJSON();
      data.baseUrl = window.World.BASE_URL;
      $(this.el).html(this.challengeItemTemplate(data));
      return this;
    }
  });
  return ChallengeItemView;
});
