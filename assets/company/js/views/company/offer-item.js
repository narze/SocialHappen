define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/offer-item.html'
], function($, _, Backbone, offerItemTemplate){
  var OfferItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    offerItemTemplate: _.template(offerItemTemplate),
    events: {
      'click a.offer': 'showEdit',
      'click': 'showEdit'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render offer item');
      var data = this.model.toJSON();
      data.baseUrl = window.Company.BASE_URL;
      $(this.el).html(this.offerItemTemplate(data));
      return this;
    },

    showEdit: function(e){
      e.preventDefault();
      console.log('show offer edit modal');
      this.options.vent.trigger('showEditRewardModal', this.model);
    }
  });
  return OfferItemView;
});
