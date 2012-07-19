define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/card-item.html',
  'text!templates/profile/card-activity-item.html'
], function($, _, Backbone, cardItemTemplate, cardActivityItemTemplate){
  var CardItemView = Backbone.View.extend({
    className: 'card',

    events: {},

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      var card = this.options.model.attributes;

      $(this.el).html(_.template(cardItemTemplate)(card));

      this.addAllActivities(card.rewards);

      return this;
    },

    addAllActivities: function(activities) {
      $('.card-activity-list', this.el).empty();
      _.each(activities, this.addOneActivity);
    },

    addOneActivity: function(activity) {
      $('.card-activity-list', this.el).append(_.template(cardActivityItemTemplate)(activity));
    }

  });
  return CardItemView;
});
