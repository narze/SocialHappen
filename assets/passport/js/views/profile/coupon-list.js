define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'text!templates/profile/coupon-list.html',
  'views/profile/coupon-item',
  'masonry'
], function($, _, Backbone, CouponModel, couponListTemplate, CouponItemView, masonry){
  var CouponListPane = Backbone.View.extend({
    couponListTemplate: _.template(couponListTemplate),

    events: {
      'click button.load-more' : 'loadMore',
      'click a.coupon-filter-all': 'showBoth',
      'click a.coupon-filter-approved': 'showApproved',
      'click a.coupon-filter-pending': 'showPending'
    },

    initialize: function(){
      _.bindAll(this);
      this.collection.bind('reset', this.addAll);
      this.collection.bind('add', this.addOne);
      // this.options.vent.bind('reloadMasonry', this.reloadMasonry);
    },

    render: function () {
      $(this.el).html(this.couponListTemplate({
      }));

      $('.pending.coupon-list', this.el).masonry({
        // options
        itemSelector : '.item',
        animationOptions: {
          duration: 400
        },
        isFitWidth: true
      });

      $('.approved.coupon-list', this.el).masonry({
        // options
        itemSelector : '.item',
        animationOptions: {
          duration: 400
        },
        isFitWidth: true
      });

      this.addAll();
      this.couponListTemp = this.collection.models;

      if(this.collection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      return this;
    },

    addOne: function(model){
      var coupon = new CouponItemView({
        model: model,
        vent: this.options.vent
      });
      var el = coupon.render().$el;
      if(model.get('confirmed')){
        $('.approved.coupon-list', this.el).append(el);
      }else{
        $('.pending.coupon-list', this.el).append(el);
      }

    },

    addAll: function(){
      $('.coupon-list', this.el).html('No coupon')

      this.collection.each(function(model){
        this.addOne(model);
      }, this);
    },

    reloadMasonry: function(){
      $('.coupon-list', this.el).masonry('reload');
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      this.collection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled');
        }else{
          button.addClass('hide');
        }

      });
    },

    showApproved: function() {
      $('.approved-coupons', this.el).show();
      $('.pending-coupons', this.el).hide();
    },

    showPending: function() {
      $('.pending-coupons', this.el).show();
      $('.approved-coupons', this.el).hide();
    },

    showBoth: function() {
      $('.pending-coupons', this.el).show();
      $('.approved-coupons', this.el).show();
    }

  });
  return CouponListPane;
});
