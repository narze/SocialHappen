define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'text!templates/company/coupon-list.html',
  'views/company/coupon-item',
  'events'
], function($, _, Backbone, CouponModel, couponListTemplate, CouponItemView, vent){
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
      this.collection.unbind('reset').bind('reset', this.addAll);
      this.collection.unbind('add').bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(this.couponListTemplate({
      }));

      this.addAll();
      this.couponListTemp = this.collection.models;

      if(this.collection.model.length <= 30){
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
      $('.coupon-list', this.el).html('');

      if(this.collection.models.length === 0){
        $('.coupon-list', this.el).html('Your company have no coupon.');
      }

      this.collection.each(function(model){
        this.addOne(model);
      }, this);
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

    loadAll: function() {
      this.collection.loadAll();
    },

    loadConfirmed: function() {
      this.collection.reset(this.couponListTemp);

      var confirmedCoupons = this.collection.select(function(coupon) {
        return coupon.get('confirmed')
      });

      this.collection.reset(confirmedCoupons);
      this.addAll();
    },

    loadNotConfirmed: function() {
      this.collection.reset(this.couponListTemp);

      var notConfirmedCoupons = this.collection.select(function(coupon) {
        return !coupon.get('confirmed')
      });

      this.collection.reset(notConfirmedCoupons);
      this.addAll();
    },

    searchCoupon: function(e) {
      e.preventDefault();

      this.collection.reset(this.couponListTemp);
      var search_texts = {
        id: $('#coupon-search-id', this.el).val(),
        name: $('#coupon-search-name', this.el).val(),
        email: $('#coupon-search-email', this.el).val(),
        phone: $('#coupon-search-phone', this.el).val()
      }

      var search_results = this.collection.select(function(coupon) {
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

      this.collection.reset(search_results);
      this.addAll();
    }

  });
  return CouponListPane;
});
