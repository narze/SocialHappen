define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/challenge-list.html',
  'views/company/challenge-item',
  'masonry',
  'endlessscroll',
  'events',
  'sandbox'
], function($, _, Backbone, ChallengeModel, challengeListTemplate, ChallengeItemView, masonry, endlessscroll, vent, sandbox){
  var ChallengeListPane = Backbone.View.extend({
    challengeListTemplate: _.template(challengeListTemplate),

    events: {
      'click button.add-challenge': 'showAddChallenge',
      'click button.load-more' : 'loadMore'
    },

    initialize: function(){
      _.bindAll(this);
      vent.bind('reloadMasonry', this.reloadMasonry);
      sandbox.collections.challengesCollection.bind('reset', this.addAll);
      sandbox.collections.challengesCollection.bind('add', this.addOne);
      sandbox.collections.challengesCollection.fetch();
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

      if(sandbox.collections.challengesCollection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      var self = this;

      // console.log('bind endless scroll');
      // $(window).endlessScroll({
        // bottomPixels: 50,
        // fireDelay: 100,
        // callback: function(fireSequence, pageSequence){
          // console.log('start load more. fireSequence:', fireSequence, 'pageSequence:', pageSequence);
          // sandbox.collections.loadMore(function(){
            // console.log('load more done. fireSequence:', fireSequence, 'pageSequence:', pageSequence);
          // });
          // return true;
        // }
      // });
      return this;
    },

    addOne: function(model){
      if(sandbox.collections.challengesCollection.models.length === 1){
        $('.tile-list', this.el).html('');
      }

      var challenge = new ChallengeItemView({
        model: model,
        vent: vent
      });
      // console.log($('.tile-list', this.el));
      var el = challenge.render().$el;
      $('.tile-list', this.el).append(el);
    },

    addAll: function(){
      console.log('addAll');
      $('.tile-list', this.el).html('');

      if(sandbox.collections.challengesCollection.models.length === 0){
        $('.tile-list', this.el).html('Your company have no challenge. Start creating a challenge by clicking "Create Challenge" button.');
      }

      sandbox.collections.challengesCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    reloadMasonry: function(){
      $('.tile-list', this.el).masonry('reload');
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.challengesCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled');
        }else{
          button.addClass('hide');
        }

      });
    },

    showAddChallenge: function(){
      console.log('show add challenge');
      var newModel = new ChallengeModel({});
      newModel.set({
        detail: {
          name: 'Challenge Name',
          description: 'Challenge Description',
          image: 'https://lh3.googleusercontent.com/XBLfCOS_oKO-XjeYiaOAuIdukQo9wXMWsdxJZLJO8hvWMBLFwCU3r_0BrRMn_c0TnEDarKuxDg=s640-h400-e365'
        },
        hash: null,
        criteria: [],
        reward_items: [],
        active: true,
        repeat: null
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      vent.trigger('showAddModal', newModel);
      vent.trigger('showRecipeModal');
    },

    clean: function() {
      this.remove();
      this.unbind();
      vent.unbind('reloadMasonry');
      sandbox.collections.challengesCollection.unbind();
    }
  });
  return ChallengeListPane;
});
