define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'text!templates/profile/coupon-list.html',
  'views/profile/coupon-item'
], function($, _, Backbone, CouponModel, couponListTemplate, CouponItemView){
  var CouponListPane = Backbone.View.extend({
    couponListTemplate: _.template(couponListTemplate),
    
    events: {
      'click button.load-more' : 'loadMore'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.collection.bind('reset', this.addAll);
      this.collection.bind('add', this.addOne);
    },
    
    render: function () {
      $(this.el).html(this.couponListTemplate({
      }));
      
      this.addAll();
      
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
    }

  });
  return CouponListPane;
});
