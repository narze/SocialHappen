define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/coupon-item.html',
  'text!templates/company/coupon-modal.html'
], function($, _, Backbone, couponItemTemplate, couponModalTemplate){
  var CouponItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    couponItemTemplate: _.template(couponItemTemplate),
    couponModalTemplate: _.template(couponModalTemplate),
    events: {
      'click .coupon-view ': 'viewCouponModal'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
      this.model.bind('view', this.viewCouponModal);
    },
    render: function () {
      var data = this.model.toJSON();
      data.baseUrl = window.Company.BASE_URL;
      $(this.el).html(this.couponItemTemplate(data));
      return this;
    },
    viewCouponModal: function(e) {
      if(e) { e.preventDefault(); }
      var data = this.model.toJSON(),
        self = this;

      //View reward
      $.ajax({
        type: 'GET',
        url: window.Company.BASE_URL + 'apiv3/reward_item/' + self.model.get('reward_item_id'),
        dataType: 'json',
        success: function(res) {
          if(res.success) {
            data.reward = res.data;
          }
          $('#coupon-modal').html(self.couponModalTemplate(data)).modal('show');

          $('#coupon-modal .coupon-approve').unbind('click').bind('click', self.approveCoupon);
        }
      })
    },
    approveCoupon: function(e){
      e.preventDefault();
      var model = this.model;
      $.ajax({
        type: 'POST',
        url: window.Company.BASE_URL + 'apiv3/confirm_coupon',
        dataType: 'json',
        data: {
          coupon_id: this.model.get('_id').$id
        },
        success: function(res) {
          model.set(res.coupon);
          model.change();
        }
      });
    }
  });
  return CouponItemView;
});
