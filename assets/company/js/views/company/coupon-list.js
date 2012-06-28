define([
  'jquery',
  'underscore',
  'backbone',
  'models/coupon',
  'text!templates/company/coupon-list.html',
  'views/company/coupon-item',
  'masonry',
  'endlessscroll'
], function($, _, Backbone, CouponModel, couponListTemplate, CouponItemView, masonry, endlessscroll){
  var CouponListPane = Backbone.View.extend({
    couponListTemplate: _.template(couponListTemplate),
    
    events: {
      'click button.load-more' : 'loadMore'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('reloadMasonry', this.reloadMasonry);
      this.collection.bind('reset', this.addAll);
      this.collection.bind('add', this.addOne);
    },
    
    render: function () {
      $(this.el).html(this.couponListTemplate({
      }));
      
      $('.tile-list', this.el).masonry({
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
      // console.log('add one coupon:', model.toJSON());
      
      var coupon = new CouponItemView({
        model: model,
        vent: this.options.vent
      });
      // console.log($('.tile-list', this.el));
      var el = coupon.render().$el;
      $('.tile-list', this.el).append(el);
    },
    
    addAll: function(){
      $('.tile-list', this.el).html('');
      this.collection.each(function(model){
        this.addOne(model);
      }, this);
    },
    
    reloadMasonry: function(){
      $('.tile-list', this.el).masonry('reload');
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
