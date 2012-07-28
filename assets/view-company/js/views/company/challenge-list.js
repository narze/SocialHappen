define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/challenge-list.html',
  'views/company/challenge-item',
  'masonry',
  'endlessscroll'
], function($, _, Backbone, ChallengeModel, challengeListTemplate, ChallengeItemView, masonry, endlessscroll){
  var ChallengeListPane = Backbone.View.extend({
    challengeListTemplate: _.template(challengeListTemplate),

    events: {
      'click button.add-challenge': 'showAddChallenge',
      'click button.load-more' : 'loadMore'
    },

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

      if(this.collection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      var self = this;

      // console.log('bind endless scroll');
      // $(window).endlessScroll({
        // bottomPixels: 50,
        // fireDelay: 100,
        // callback: function(fireSequence, pageSequence){
          // console.log('start load more. fireSequence:', fireSequence, 'pageSequence:', pageSequence);
          // self.collection.loadMore(function(){
            // console.log('load more done. fireSequence:', fireSequence, 'pageSequence:', pageSequence);
          // });
          // return true;
        // }
      // });
      return this;
    },

    addOne: function(model){
      // console.log('add one challenge:', model.toJSON());

      var challenge = new ChallengeItemView({
        model: model,
        vent: this.options.vent
      });
      // console.log($('.tile-list', this.el));
      var el = challenge.render().$el;
      $('.tile-list', this.el).append(el);
    },

    addAll: function(){
      $('.tile-list', this.el).html('');

      if(this.collection.models.length == 0){
        $('.tile-list', this.el).html('This company have no challenge.');
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
        active: true,
        repeat: null
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      this.options.vent.trigger('showAddModal', newModel);
    }
  });
  return ChallengeListPane;
});
