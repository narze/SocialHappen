define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/reward-item.html'
], function($, _, Backbone, rewardItemTemplate){
  var RewardItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    rewardItemTemplate: _.template(rewardItemTemplate),
    events: {
      'click a.reward': 'showEdit'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render reward item');
      var data = this.model.toJSON();
      data.baseUrl = window.Company.BASE_URL;
      data.redeemed = data.redeemed || false;
      data.usedup = data.redeem.amount_remain === 0;
      $(this.el).html(this.rewardItemTemplate(data));
      return this;
    },
    
    showEdit: function(e){
      e.preventDefault();
      console.log('show reward edit modal');
      this.options.vent.trigger('showEditRewardModal', this.model);
    }
  });
  return RewardItemView;
});
