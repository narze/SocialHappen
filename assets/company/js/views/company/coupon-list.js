define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'text!templates/company/coupon-list.html',
  'views/company/coupon-item'
], function($, _, Backbone, CouponModel, couponListTemplate, CouponItemView){
  var CouponListPane = Backbone.View.extend({
    couponListTemplate: _.template(couponListTemplate),

    events: {
      'click button.load-more' : 'loadMore',
      'click a.coupon-filter-all': 'loadAll',
      'click a.coupon-filter-confirmed': 'loadConfirmed',
      'click a.coupon-filter-not-confirmed': 'loadNotConfirmed',
      'keyup .coupon-search-text': 'searchCoupon'
    },

    couponListTemp: [],

    initialize: function(){
      _.bindAll(this);
      this.collection.bind('reset', this.addAll);
      this.collection.bind('add', this.addOne);
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
        vent: this.options.vent
      });
      // console.log($('.coupon-list', this.el));
      var el = coupon.render().$el;
      $('.coupon-list', this.el).append(el);
    },

    addAll: function(){
      $('.coupon-list', this.el).html('');
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
      this.collection.loadConfirmed();
    },

    loadNotConfirmed: function() {
      this.collection.loadNotConfirmed();
    },

    searchCoupon: function() {
      this.collection.reset(this.couponListTemp);
      var search_text = $('input.coupon-search-text', this.el).val();
      var search_results = this.collection.select(function(coupon) {
        coupon.user = coupon.get('user');
        var search_fields = [coupon.id, coupon.user.user_first_name, coupon.user.user_last_name, coupon.user.user_email, coupon.user.user_phone];
        for(var i in search_fields) {
          var field = search_fields[i];
          if(field.search(new RegExp(search_text, 'i')) !== -1) {
            return true;
          }
        }
        return false;
      });
      this.collection.reset(search_results);
      this.addAll();
    }

  });
  return CouponListPane;
});
