define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/coupon-item.html'
], function($, _, Backbone, couponItemTemplate){
  var CouponItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    couponItemTemplate: _.template(couponItemTemplate),
    events: {
      'click button.coupon-approve': 'approveCoupon'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render coupon item');
      var data = this.model.toJSON();
      data.baseUrl = window.Company.BASE_URL;
      $(this.el).html(this.couponItemTemplate(data));
      return this;
    },
    approveCoupon: function(e){
      e.preventDefault();
      console.log('approve coupon');
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
