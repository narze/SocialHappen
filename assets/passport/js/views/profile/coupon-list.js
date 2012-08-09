define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'text!templates/profile/coupon-list.html',
  'views/profile/coupon-item',
  'masonry',
  'sandbox',
  'events'
], function($, _, Backbone, CouponModel, couponListTemplate, CouponItemView, masonry, sandbox, vent){
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
      sandbox.collections.couponCollection.bind('reset', this.addAll);
      sandbox.collections.couponCollection.bind('add', this.addOne);
      sandbox.collections.couponCollection.fetch();
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
      this.couponListTemp = sandbox.collections.couponCollection.models;

      if(sandbox.collections.couponCollection.models.length <= 30){
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

      var approved = 0;
      var notApproved = 0;
      sandbox.collections.couponCollection.each(function(model){
        if(model.get('confirmed')){
          approved++;
        }else{
          notApproved++;
        }
      }, this);

      $('.coupon-list', this.el).html('');

      if(approved === 0){
        $('.approved.coupon-list', this.el).html('No coupon');
      }
      if(notApproved === 0){
        $('.pending.coupon-list', this.el).html('No coupon');
      }

      sandbox.collections.couponCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    reloadMasonry: function(){
      $('.coupon-list', this.el).masonry('reload');
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.couponCollection.loadMore(function(loaded){
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
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.collections.couponCollection.unbind();
    },

    showCouponModal: function(rewardItemId) {
      var model = _.find(sandbox.collections.couponCollection.models, function(model) { return model.get('reward_item_id') === rewardItemId; });
      vent.trigger('viewRewardByModel', model);
    }

  });
  return CouponListPane;
});
