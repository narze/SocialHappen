define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/coupon-item.html',
  'text!templates/profile/reward-item-modal.html'
], function($, _, Backbone, couponItemTemplate, rewardItemTemplate){
  var CouponItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    couponItemTemplate: _.template(couponItemTemplate),
    rewardItemTemplate: _.template(rewardItemTemplate),
    events: {
      'click button.view-reward': 'viewReward'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render coupon item');
      var data = this.model.toJSON();
      data.baseUrl = window.Passport.BASE_URL;
      $(this.el).html(this.couponItemTemplate(data));
      return this;
    },
    viewReward: function() {
      var reward_item_id = this.model.get('reward_item_id');
      var self = this;
      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: window.Passport.BASE_URL + 'apiv3/reward_item/' + reward_item_id,
        success: function(res) {
          var data = res.data;
          data.coupon = self.model.toJSON();
          data.couponId = self.model.id;
          $('#reward-item').html(self.rewardItemTemplate(data)).modal();
        }
      });
    }
  });
  return CouponItemView;
});
