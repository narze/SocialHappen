define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'text!templates/company/coupon-list.html',
  'views/company/coupon-item',
  'events',
  'sandbox'
], function($, _, Backbone, CouponModel, couponListTemplate, CouponItemView, vent, sandbox){
  var CouponListPane = Backbone.View.extend({
    couponListTemplate: _.template(couponListTemplate),

    events: {
      'click button.load-more' : 'loadMore',
      'click a.coupon-filter-all': 'loadAll',
      'click a.coupon-filter-confirmed': 'loadConfirmed',
      'click a.coupon-filter-not-confirmed': 'loadNotConfirmed',
      'click button#coupon-search': 'searchCoupon'
    },

    couponListTemp: [],

    initialize: function(){
      _.bindAll(this);
      sandbox.collections.couponsCollection.bind('reset', this.addAll);
      sandbox.collections.couponsCollection.bind('add', this.addOne);
      sandbox.collections.couponsCollection.fetch();
    },

    render: function () {
      $(this.el).html(this.couponListTemplate({
      }));

      this.addAll();
      this.couponListTemp = sandbox.collections.couponsCollection.models;

      if(sandbox.collections.couponsCollection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      return this;
    },

    addOne: function(model){
      // console.log('add one coupon:', model.toJSON());

      var coupon = new CouponItemView({
        model: model,
        vent: vent
      });
      // console.log($('.coupon-list', this.el));
      var el = coupon.render().$el;
      $('.coupon-list', this.el).append(el);
    },

    addAll: function(){
      console.log('addAll');
      $('.coupon-list', this.el).html('');

      if(sandbox.collections.couponsCollection.models.length === 0){
        $('.coupon-list', this.el).html('Your company have no coupon.');
      }

      sandbox.collections.couponsCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.couponsCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled');
        }else{
          button.addClass('hide');
        }

      });
    },

    loadAll: function() {
      sandbox.collections.couponsCollection.loadAll();
    },

    loadConfirmed: function() {
      sandbox.collections.couponsCollection.reset(this.couponListTemp);

      var confirmedCoupons = sandbox.collections.couponsCollection.select(function(coupon) {
        return coupon.get('confirmed')
      });

      sandbox.collections.couponsCollection.reset(confirmedCoupons);
      this.addAll();
    },

    loadNotConfirmed: function() {
      sandbox.collections.couponsCollection.reset(this.couponListTemp);

      var notConfirmedCoupons = sandbox.collections.couponsCollection.select(function(coupon) {
        return !coupon.get('confirmed')
      });

      sandbox.collections.couponsCollection.reset(notConfirmedCoupons);
      this.addAll();
    },

    searchCoupon: function(e) {
      e.preventDefault();

      sandbox.collections.couponsCollection.reset(this.couponListTemp);
      var search_texts = {
        id: $('#coupon-search-id', this.el).val(),
        name: $('#coupon-search-name', this.el).val(),
        email: $('#coupon-search-email', this.el).val(),
        phone: $('#coupon-search-phone', this.el).val()
      }

      var search_results = sandbox.collections.couponsCollection.select(function(coupon) {
        coupon.user = coupon.get('user');
        var search_datas = {
          id: coupon.id,
          name: coupon.user.user_first_name + coupon.user.user_last_name,
          email: coupon.user.user_email,
          phone: coupon.user.user_phone
        }
        var search_fields = ['id', 'name', 'email', 'phone']

        for(var i in search_fields) {
          var field = search_fields[i];
          if(search_texts[field] !== '') {
            if(search_datas[field].search(new RegExp(search_texts[field], 'i')) === -1) {
              return false;
            }
          }
        }
        return true;
      });

      sandbox.collections.couponsCollection.reset(search_results);
      this.addAll();
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.collections.couponsCollection.unbind();
    }

  });
  return CouponListPane;
});
