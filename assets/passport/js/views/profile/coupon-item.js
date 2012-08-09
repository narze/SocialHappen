define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/coupon-item.html',
  'text!templates/profile/reward-item-modal.html',
  'events'
], function($, _, Backbone, couponItemTemplate, rewardItemTemplate, vent){
  var CouponItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    couponItemTemplate: _.template(couponItemTemplate),
    rewardItemTemplate: _.template(rewardItemTemplate),
    events: {
      'click button.view-reward': 'viewReward',
      'click a.reward': 'viewReward',
      'click': 'viewReward'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);

      vent.bind('viewRewardByModel', this.viewRewardByModel);
    },
    render: function () {
      console.log('render coupon item');
      var data = this.model.toJSON();
      data.baseUrl = window.Passport.BASE_URL;
      $(this.el).html(this.couponItemTemplate(data));
      return this;
    },
    viewReward: function(e) {
      if(e) { e.preventDefault(); }
      var data = this.model.get('reward_item') || {};
      data.coupon = this.model.toJSON();
      data.couponId = this.model.id;
      $('#reward-item').html(this.rewardItemTemplate(data)).modal();
    },
    viewRewardByModel: function(model) {
      var data = model.get('reward_item') || {};
      data.coupon = model.toJSON();
      data.couponId = model.id;
      $('#reward-item').html(this.rewardItemTemplate(data)).modal();
    }
  });
  return CouponItemView;
});
