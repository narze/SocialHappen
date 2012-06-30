define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/carousel.html'
], function($, _, Backbone, carouselTemplate){
  var CarouselView = Backbone.View.extend({
    carouselTemplate: _.template(carouselTemplate),
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.carouselTemplate({
        
      }));
      
      return this;
    }
  });
  return CarouselView;
});
