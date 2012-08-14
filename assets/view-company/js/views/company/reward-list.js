define([
  'jquery',
  'underscore',
  'backbone',
  'models/reward',
  'text!templates/company/reward-list.html',
  'views/company/reward-item',
  'masonry',
  'endlessscroll'
], function($, _, Backbone, RewardModel, rewardListTemplate, RewardItemView, masonry, endlessscroll){
  var RewardListPane = Backbone.View.extend({
    rewardListTemplate: _.template(rewardListTemplate),

    events: {
      'click button.load-more' : 'loadMore'
    },

    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('reloadMasonry', this.reloadMasonry);
      this.collection.bind('reset', this.render);
      this.collection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(this.rewardListTemplate({
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

      if(this.collection.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      } else {
        $('button.load-more', this.el).removeClass('hide');
      }

      var self = this;

      //Check if each reward are redeemed
      $.ajax({
        dataType: 'json',
        type: 'POST',
        url: window.Passport.BASE_URL + 'apiv3/rewardsRedeemed',
        success: function(res) {
          if(res.success) {
            for (var i = 0, len = res.data.length; i < len; i++) {
              var model = self.collection.get(res.data[i]);

              if(model) {
                console.log('rewardsRedeemed', model.get('name'));
                model.set('redeemed', true);
                model.change();
              }
            }
          }
        }
      });

      return this;
    },

    addOne: function(model){
      // console.log('add one reward:', model.toJSON());

      var reward = new RewardItemView({
        model: model,
        vent: this.options.vent
      });
      // console.log($('.tile-list', this.el));
      var el = reward.render().$el;
      $('.tile-list', this.el).append(el);
    },

    addAll: function(){
      $('.tile-list', this.el).html('');

      if(this.collection.length === 0){
        $('.tile-list', this.el).html('This company have no reward.');
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
  return RewardListPane;
});
