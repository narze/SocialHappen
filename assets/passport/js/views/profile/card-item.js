define([
  'jquery',
  'underscore',
  'backbone',
  'bootstrap',
  'text!templates/profile/card-item.html',
  'text!templates/profile/card-activity-item.html'
], function($, _, Backbone, bootstrap, cardItemTemplate, cardActivityItemTemplate){
  var CardItemView = Backbone.View.extend({
    className: 'card',

    events: {
      'click a.coupon': 'onClickCoupon'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      var card = this.options.model.attributes;

      $(this.el).html(_.template(cardItemTemplate)(card));

      this.addAllActivities(card.coupons);

      return this;
    },

    addAllActivities: function(activities) {
      $('.card-activity-list', this.el).empty();
      _.each(activities, this.addOneActivity);
      this.$('a.coupon').popover({
        placement: 'top'
      });
    },

    addOneActivity: function(activity) {
      console.log(activity);
      activity.reward_item.reward_item_id = activity.reward_item_id;
      activity.reward_item.time = moment.unix(activity.timestamp).fromNow();
      $('.card-activity-list', this.el).append(_.template(cardActivityItemTemplate)(activity.reward_item));
    },

    onClickCoupon: function(){
      this.$('a.coupon').popover('hide');
    }

  });
  return CardItemView;
});
