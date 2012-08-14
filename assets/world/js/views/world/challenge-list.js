define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/world/challenge-list.html',
  'views/world/challenge-item',
  'masonry',
  'endlessscroll'
], function($, _, Backbone, challengeListTemplate, ChallengeItemView, masonry, endlessscroll){
  var ChallengeListPane = Backbone.View.extend({
    challengeListTemplate: _.template(challengeListTemplate),

    events: {
      'click button.load-more' : 'loadMore'
    },

    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('reloadMasonry', this.reloadMasonry);
      this.collection.bind('reset', this.addAll);
      this.collection.bind('add', this.addOne);
    },

    render: function () {
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
      // console.log('add one challenge:', model.toJSON());

      var challenge = new ChallengeItemView({
        model: model
      });
      // console.log($('.tile-list', this.el));
      var el = challenge.render().$el;
      $('.tile-list', this.el).append(el);
    },

    addAll: function(){
      //Reset
      $(this.el).html(this.challengeListTemplate());

      if(this.collection.length === 0){
        $('.tile-list', this.el).html('No challenge.');
      }

      if(this.collection.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      } else {
        $('button.load-more', this.el).removeClass('hide');
      }

      this.collection.each(function(model){
        this.addOne(model);
      }, this);
    },

    reloadMasonry: function(){
      $('.tile-list', this.el).masonry('reload');
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      this.collection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled hide');
        }else{
          button.addClass('hide');
        }

      });
    }
  });
  return ChallengeListPane;
});
