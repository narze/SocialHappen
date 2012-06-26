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
      $(this.el).html(this.rewardItemTemplate(data));
      return this;
    },
    
    showEdit: function(e){
      e.preventDefault();
      this.options.vent.trigger('showEditModal', this.model);
    }
  });
  return RewardItemView;
});
