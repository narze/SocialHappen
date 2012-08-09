define([
  'jquery',
  'underscore',
  'backbone',
  'models/reward',
  'text!templates/company/reward-list.html',
  'views/company/reward-item',
  'masonry',
  'endlessscroll',
  'events'
], function($, _, Backbone, RewardModel, rewardListTemplate, RewardItemView, masonry, endlessscroll, vent){
  var RewardListPane = Backbone.View.extend({
    rewardListTemplate: _.template(rewardListTemplate),

    events: {
      'click button.add-reward': 'showAddReward',
      'click button.load-more' : 'loadMore'
    },

    initialize: function(){
      _.bindAll(this);
      vent.unbind('reloadMasonry').bind('reloadMasonry', this.reloadMasonry);
      this.collection.unbind('reset').bind('reset', this.addAll);
      this.collection.unbind('add').bind('add', this.addOne);
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

      if(this.collection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      return this;
    },

    addOne: function(model){

      if(this.collection.models.length === 1){
        $('.tile-list', this.el).html('');
      }

      var reward = new RewardItemView({
        model: model,
        vent: vent
      });

      var el = reward.render().$el;
      $('.tile-list', this.el).append(el);
    },

    addAll: function(){
      $('.tile-list', this.el).html('');

      if(this.collection.models.length == 0){
        $('.tile-list', this.el).html('Your company have no reward. Start creating a reward by clicking "Create Reward" button.');
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
          button.removeClass('disabled');
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
        redeem: {
          point: 10,
          amount: 10,
          amount_remain: 10,
          once: true
        },
        status: 'published'
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      vent.trigger('showAddRewardModal', newModel);
    }
  });
  return RewardListPane;
});
