define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/card-item',
  'text!templates/profile/card-list.html',
  'events',
  'sandbox'
], function($, _, Backbone, CardItemView, cardListTemplate, vent, sandbox){
  var CardListPane = Backbone.View.extend({

    events: {
      'click .card': 'showCard'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.collections.cardCollection.bind('reset', this.addAll);
      sandbox.collections.cardCollection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(_.template(cardListTemplate));
      sandbox.collections.cardCollection.fetch()
      return this;
    },

    addAll: function() {
      console.log('addAll');
      if(sandbox.collections.cardCollection.length === 0){
        $('#card-list', this.el).html('No card');
      } else {
        $('#card-list', this.el).empty();
      }

      sandbox.collections.cardCollection.each(this.addOne);
    },

    addOne: function(model) {
      model.set('user', sandbox.models.currentUserModel.attributes)
      var card = new CardItemView({
        model: model,
        vent: vent
      });

      var cardView = card.render();
      $('#card-list', this.el).append(cardView.el);
    },

    showCard: function(e) {
      $(e.currentTarget).addClass('open').siblings().removeClass('open');
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.collections.cardCollection.unbind();
    }

  });
  return CardListPane;
});
