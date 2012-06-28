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
      'click a.coupon': 'showEdit'
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
    
    showEdit: function(e){
      e.preventDefault();
      console.log('show coupon edit modal');
      this.options.vent.trigger('showEditCouponModal', this.model);
    }
  });
  return CouponItemView;
});
