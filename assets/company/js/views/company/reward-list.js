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
      'click button.add-reward': 'showAddReward',
      'click button.load-more' : 'loadMore'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('reloadMasonry', this.reloadMasonry);
      this.collection.bind('reset', this.addAll);
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
      
      if(this.collection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }
      
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
        detail: {
          name: 'Challenge Name',
          description: 'Challenge Description',
          image: 'https://lh3.googleusercontent.com/XBLfCOS_oKO-XjeYiaOAuIdukQo9wXMWsdxJZLJO8hvWMBLFwCU3r_0BrRMn_c0TnEDarKuxDg=s640-h400-e365'
        },
        hash: null,
        criteria: [],
        active: true,
        repeat: null
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      this.options.vent.trigger('showAddModal', newModel);
    }
  });
  return RewardListPane;
});
