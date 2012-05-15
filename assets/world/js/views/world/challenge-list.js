define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/world/challenge-list.html',
  'views/world/challenge-item',
  'masonry'
], function($, _, Backbone, challengeListTemplate, ChallengeItemView, masonry){
  var ChallengeListPane = Backbone.View.extend({
    challengeListTemplate: _.template(challengeListTemplate),
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('reloadMasonry', this.reloadMasonry);
      this.collection.bind('reset', this.addAll);
      this.collection.bind('add', this.addOne);
    },
    
    render: function () {
      $(this.el).html(this.challengeListTemplate({
      }));
      
      $('.tile-list', this.el).masonry({
        // options
        itemSelector : '.item',
        animationOptions: {
          duration: 400
        },
        isFitWidth: true
      });
      
      this.addAll();
      
      return this;
    },
    
    addOne: function(model){
      console.log('add one challenge:', model.toJSON());
      
      var challenge = new ChallengeItemView({
        model: model
      });
      // console.log($('.tile-list', this.el));
      $('.tile-list', this.el).append(challenge.render().el);
    },
    
    addAll: function(){
      this.collection.each(function(model){
        this.addOne(model);
      }, this);
    },
    
    reloadMasonry: function(){
      $('.tile-list', this.el).masonry('reload');
    }
  });
  return ChallengeListPane;
});
