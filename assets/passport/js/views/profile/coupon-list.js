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
      'click button.load-more' : 'loadMore'
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

      $('.coupon-list', this.el).masonry({
        // options
        itemSelector : '.item',
        animationOptions: {
          duration: 400
        },
        isFitWidth: true
      });
      
      this.addAll();
      
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
      $('.coupon-list', this.el).append(el);
    },
    
    addAll: function(){
      $('.coupon-list', this.el).html('');
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
    }

  });
  return CouponListPane;
});
