define([
  'jquery',
  'underscore',
  'backbone',
  'collections/cards',
  'text!templates/profile/card-list.html',
  'text!templates/profile/card-item.html'
], function($, _, Backbone, CardsCollection, cardListTemplate, cardItemTemplate){
  var CardListPane = Backbone.View.extend({

    events: {
      'click .card': 'showCard'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      this.$el.html(_.template(cardListTemplate));
      var cardsCollection = new CardsCollection();
      var self = this;
      cardsCollection.fetch({
        success: function() {
          self.addAll(cardsCollection);
        }
      });

      return this;
    },

    addAll: function(collection) {
      collection.each(function(model) {
        this.addOne(model);
      }, this);
    },

    addOne: function(model) {
      var card = model.attributes;
      card.user = this.options.currentUserModel.attributes;
      $('.card-list', this.el).append(_.template(cardItemTemplate)(card));
    },

    showCard: function(e) {
      $(e.currentTarget).addClass('open').siblings().removeClass('open');
    }

  });
  return CardListPane;
});
