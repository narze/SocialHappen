define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/coupon.html',
  'sandbox'
], function($, _, Backbone, couponTemplate, sandbox){
  var ProfilePage = Backbone.View.extend({
    couponTemplate: _.template(couponTemplate),
    el: '#content',

    events: {

    },

    initialize: function(){
      _.bindAll(this)
      sandbox.collections.couponCollection.bind('reset', this.add)
    },

    render: function () {
      $('#header').remove()
    },

    add: function() {
      var coupon = sandbox.collections.couponCollection.get(sandbox.couponId).toJSON()
      $(this.el).html(this.couponTemplate(coupon))
    }
  })
  return ProfilePage
})
