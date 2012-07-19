define([
  'jquery',
  'underscore',
  'backbone',
  'masonry',
  'text!templates/profile/card-item.html',
  'text!templates/profile/card-activity-item.html'
], function($, _, Backbone, masonry, cardItemTemplate, cardActivityItemTemplate){
  var CardItemView = Backbone.View.extend({
    className: 'card',

    events: {},

    initialize: function(){
      _.bindAll(this);

      this.options.vent.bind('reloadMasonry', this.reloadMasonry);
    },

    render: function () {
      var card = this.options.model.attributes;

      $(this.el).html(_.template(cardItemTemplate)(card));
      $('.card-activity-list', this.el).masonry({
        itemSelector: '.activity',
        animationOptions: {
          duration: 400
        }
      });

      this.addAllActivities(card.rewards);

      return this;
    },

    addAllActivities: function(activities) {
      $('.card-activity-list', this.el).empty();
      _.each(activities, this.addOneActivity);
    },

    addOneActivity: function(activity) {
      $('.card-activity-list', this.el).append(_.template(cardActivityItemTemplate)(activity));
    },

    reloadMasonry: function() {
      $('.card-activity-list', this.el).masonry('reload');
    }

  });
  return CardItemView;
});
