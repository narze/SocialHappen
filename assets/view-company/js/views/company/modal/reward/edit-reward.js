define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/reward/edit-reward.html',
  'jqueryui'
], function($, _, Backbone, editTemplate, jqueryui){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    events: {
      'click button.redeem-reward': 'redeemReward'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showEditRewardModal', this.show);
    },

    render: function () {
      console.log('render modal');
      
      if(!this.model){
        return;
      }
      
      var data = this.model.toJSON();
      $(this.el).html(this.editTemplate(data));
      
      return this;
    },
    
    show: function(model){
      this.showEdit(model);
    },

    showEdit: function(model) {
      this.model = model;
      console.log('show edit modal:', model.toJSON());
      this.render();
      
      this.$el.modal('show');
    },

    redeemReward: function(model) {
      console.log('Redeeming reward');
    }
  });
  return EditModalView;
});
