define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/player/challenge-item.html'
], function($, _, Backbone, challengeItemTemplate){
  var ChallengeItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    challengeItemTemplate: _.template(challengeItemTemplate),
    events: {
      'click a.challenge': 'showEdit'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render challenge item');
      var data = this.model.toJSON();
      data.baseUrl = window.World.BASE_URL;
      $(this.el).html(this.challengeItemTemplate(data));
      return this;
    },
    
    showEdit: function(e){
      e.preventDefault();
      this.options.vent.trigger('showEditModal', this.model);
    }
  });
  return ChallengeItemView;
});
