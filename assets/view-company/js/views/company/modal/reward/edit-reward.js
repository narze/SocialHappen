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
    
    showEditName: function(){
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
      $('input.reward-name', this.el).focus();
    },
    
    saveEditName: function(){
      
      var name = $('input.reward-name', this.el).val();
      
      this.model.set('name', name).trigger('change');
      this.model.save();
      
      $('h3.edit-name', this.$el).show();
      $('div.edit-name', this.$el).hide();
      
      this.options.vent.trigger('showEditRewardModal', this.model);
    },
    
    setRewardStatus: function(){
      var status = $('select.reward-status', this.el).val();
      console.log('set reward status to', status);
      
      this.model.set('status', status).trigger('change');
      this.model.save();
    },
    
    showEditDescription: function(){
      $('div.edit-description', this.el).hide();
      $('div.edit-description-field', this.el).show();
    },
    
    saveEditDescription: function(){
      
      var description = $('textarea.reward-description', this.el).val();
      
      this.model.set('description', description).trigger('change');
      this.model.save();
      
      $('div.edit-description', this.el).show();
      $('div.edit-description-field', this.el).hide();
      
      this.options.vent.trigger('showEditRewardModal', this.model);
    },
    
    showEditImage: function(){
      $('div.edit-image', this.el).show();
    },
    
    saveEditImage: function(){
      $('div.edit-image', this.el).hide();
      
      var image = $('input.reward-image', this.el).val();
      
      this.model.set('image', image).trigger('change');
      this.model.save();
      
      this.options.vent.trigger('showEditRewardModal', this.model);
    },
    
    showEditRedeem: function(){
      $('div.edit-redeem', this.el).hide();
      $('div.edit-redeem-field', this.el).show();
    },
    
    saveEditRedeem: function(){
      
      var amount = $('input.reward-amount', this.el).val();
      var point = $('input.reward-point', this.el).val();
      var once = !_.isUndefined($('input.reward-once', this.el).attr('checked'));
      
      var redeem = this.model.get('redeem');
      
      redeem.amount = amount;
      redeem.point = point;
      redeem.once = once;
      
      console.log('set redeem:', redeem)
      
      this.model.set('redeem', redeem).trigger('change');
      this.model.save();
      
      $('div.edit-redeem', this.el).show();
      $('div.edit-redeem-field', this.el).hide();
      
      this.options.vent.trigger('showEditRewardModal', this.model);
    }
  });
  return EditModalView;
});
