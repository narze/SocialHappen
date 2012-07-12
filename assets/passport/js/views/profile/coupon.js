define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/coupon.html'
], function($, _, Backbone, couponTemplate){
  var ProfilePage = Backbone.View.extend({
    couponTemplate: _.template(couponTemplate),
    el: '#content',

    events: {

    },

    initialize: function(){
      _.bindAll(this)
    },

    render: function () {
      $('#header').remove()
      console.log(this.options.couponModel.toJSON())
      $(this.el).html(this.couponTemplate(this.options.couponModel.toJSON()))
    }
  })
  return ProfilePage
})
