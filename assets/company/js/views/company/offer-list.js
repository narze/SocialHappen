define([
  'jquery',
  'underscore',
  'backbone',
  'models/offer',
  'text!templates/company/offer-list.html',
  'views/company/offer-item',
  'masonry',
  'endlessscroll',
  'events',
  'sandbox'
], function($, _, Backbone, OfferModel, offerListTemplate, OfferItemView, masonry, endlessscroll, vent, sandbox){
  var OfferListPane = Backbone.View.extend({
    offerListTemplate: _.template(offerListTemplate),

    events: {
      'click button.add-offer': 'showAddOffer',
      'click button.load-more' : 'loadMore'
    },

    initialize: function(){
      _.bindAll(this);
      vent.bind('reloadMasonry', this.reloadMasonry);
      sandbox.collections.offersCollection.bind('reset', this.addAll);
      sandbox.collections.offersCollection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(this.offerListTemplate({}));
      sandbox.collections.offersCollection.fetch();
      return this;
    },

    addOne: function(model){
      var offer = new OfferItemView({
        model: model,
        vent: vent
      });

      var el = offer.render().$el;
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

      if(sandbox.collections.offersCollection.length === 0){
        $('.tile-list', this.el).html('Your company have no offer. Start creating a offer by clicking "Create Offer" button.');
      }

      if(sandbox.collections.offersCollection.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      } else {
        $('button.load-more', this.el).removeClass('hide');
      }

      sandbox.collections.offersCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    reloadMasonry: function(){
      $('.tile-list', this.el).masonry('reload');
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.offersCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled hide');
        }else{
          button.addClass('hide');
        }

      });
    },

    showAddOffer: function(){
      console.log('show add offer');
      var newModel = new OfferModel({});
      newModel.set({
        name: 'Offer Name',
        image: 'https://lh5.googleusercontent.com/mww1eX8x-JdWhYUA1B-ovYX3MQf5gGwsqcXvySmebElaBcnKeH0wojdCDSF4rfhnAMlXvsG_=s640-h400-e365',
        value: 0,
        description: 'Offer Description',
        status: 'published',
        type: 'offer',
        redeem_method: 'in_store'
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      vent.trigger('showAddRewardModal', newModel);
    },

    clean: function() {
      this.remove();
      this.unbind();
      vent.unbind('reloadMasonry');
      sandbox.collections.offersCollection.unbind();
    }
  });
  return OfferListPane;
});
