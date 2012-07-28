define([
  'jquery',
  'underscore',
  'backbone',
  'collections/cards',
  'views/profile/card-item',
  'text!templates/profile/card-list.html'
], function($, _, Backbone, CardsCollection, CardItemView, cardListTemplate){
  var CardListPane = Backbone.View.extend({

    events: {
      'click .card': 'showCard'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      $(this.el).html(_.template(cardListTemplate));
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
      if(collection.models.length == 0){
        $('#card-list', this.el).html('No card');
      }

      collection.each(this.addOne);
    },

    addOne: function(model) {
      model.set('user', this.options.currentUserModel.attributes)
      var card = new CardItemView({
        model: model,
        vent: this.options.vent
      });

      var cardView = card.render();
      $('#card-list', this.el).append(cardView.el);
    },

    showCard: function(e) {
      $(e.currentTarget).addClass('open').siblings().removeClass('open');
    }

  });
  return CardListPane;
});
