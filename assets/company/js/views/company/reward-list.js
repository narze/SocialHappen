define([
  'jquery',
  'underscore',
  'backbone',
  'models/reward',
  'text!templates/company/reward-list.html',
  'views/company/reward-item',
  'masonry',
  'endlessscroll',
  'events',
  'sandbox'
], function($, _, Backbone, RewardModel, rewardListTemplate, RewardItemView, masonry, endlessscroll, vent, sandbox){
  var RewardListPane = Backbone.View.extend({
    rewardListTemplate: _.template(rewardListTemplate),

    events: {
      'click button.add-reward': 'showAddReward',
      'click button.load-more' : 'loadMore'
    },

    initialize: function(){
      _.bindAll(this);
      vent.bind('reloadMasonry', this.reloadMasonry);
      sandbox.collections.rewardsCollection.bind('reset', this.addAll);
      sandbox.collections.rewardsCollection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(this.rewardListTemplate({}));
      sandbox.collections.rewardsCollection.fetch();
      return this;
    },

    addOne: function(model){
      var reward = new RewardItemView({
        model: model,
        vent: vent
      });

      var el = reward.render().$el;
      $('.tile-list', this.el).append(el);
    },

    addAll: function(){
      console.log('addAll');

      $('.tile-list', this.el).masonry({
        // options
        itemSelector : '.item',
        animationOptions: {
          duration: 400
        },
        isFitWidth: true
      });

      $('.tile-list', this.el).html('');

      if(sandbox.collections.rewardsCollection.length === 0){
        $('.tile-list', this.el).html('Your company have no reward. Start creating a reward by clicking "Create Reward" button.');
      }

      if(sandbox.collections.rewardsCollection.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      } else {
        $('button.load-more', this.el).removeClass('hide');
      }

      sandbox.collections.rewardsCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    reloadMasonry: function(){
      $('.tile-list', this.el).masonry('reload');
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.rewardsCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled hide');
        }else{
          button.addClass('hide');
        }

      });
    },

    showAddReward: function(){
      console.log('show add reward');
      var newModel = new RewardModel({});
      newModel.set({
        name: 'Reward Name',
        image: 'https://lh5.googleusercontent.com/mww1eX8x-JdWhYUA1B-ovYX3MQf5gGwsqcXvySmebElaBcnKeH0wojdCDSF4rfhnAMlXvsG_=s640-h400-e365',
        value: 0,
        description: 'Reward Description',
        type: 'redeem',
        redeem: {
          point: 10,
          amount: 10,
          amount_redeemed: 0,
          once: false
        },
        status: 'published',
        redeem_method: 'in_store'
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      vent.trigger('showAddRewardModal', newModel);
    },

    clean: function() {
      this.remove();
      this.unbind();
      vent.unbind('reloadMasonry');
      sandbox.collections.rewardsCollection.unbind();
    }
  });
  return RewardListPane;
});
