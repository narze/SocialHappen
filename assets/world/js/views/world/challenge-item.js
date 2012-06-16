define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/world/challenge-item.html'
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
      var now = Math.floor(new Date().getTime()/1000);
      console.log(data.detail.name, data.start_date, data.end_date, now);
      
      data.expired = data.end_date < now;
      data.notstart = data.start_date > now;
      
      $(this.el).html(this.challengeItemTemplate(data));
      return this;
    }
  });
  return ChallengeItemView;
});
