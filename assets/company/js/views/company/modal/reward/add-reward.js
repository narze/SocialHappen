define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/modal/reward/add-reward.html',
  'jqueryui'
], function($, _, Backbone, ChallengeModel, addTemplate,
   jqueryui){
  var EditModalView = Backbone.View.extend({
    addTemplate: _.template(addTemplate),
    
    events: {
      'click h3.edit-name': 'showEditName',
      'click button.save-name': 'saveEditName',
      'click div.edit-description': 'showEditDescription',
      'click button.save-description': 'saveEditDescription',
      'click img.reward-image, h6.edit-image': 'showEditImage',
      'click button.save-image': 'saveEditImage',
      'change select.reward-status': 'setRewardStatus',
      'click .edit-redeem': 'showEditRedeem',
      'click .save-redeem': 'saveEditRedeem',
      'click button.create-reward': 'createReward'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showAddRewardModal', this.show);
    },

    render: function () {
      console.log('render modal');
      
      if(!this.model){
        return;
      }
      
      var data = this.model.toJSON();
      $(this.el).html(this.addTemplate(data));
      
      return this;
    },
    
    show: function(model){
      this.model = model;
      
      console.log('show add modal:', this.model.toJSON());
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
      
      
      $('h3.edit-name', this.$el).show();
      $('div.edit-name', this.$el).hide();
      
      this.options.vent.trigger('showAddRewardModal', this.model);
    },
    
    
    setRewardStatus: function(){
      var status = $('select.reward-status', this.el).val();
      console.log('set reward status to', status);
      
      this.model.set('status', status).trigger('change');
      
    },
    
    showEditDescription: function(){
      $('div.edit-description', this.el).hide();
      $('div.edit-description-field', this.el).show();
    },
    
    saveEditDescription: function(){
      
      var description = $('textarea.reward-description', this.el).val();
      
      this.model.set('description', description).trigger('change');
      
      
      $('div.edit-description', this.el).show();
      $('div.edit-description-field', this.el).hide();
      
      this.options.vent.trigger('showAddRewardModal', this.model);
    },
    
    showEditImage: function(){
      $('div.edit-image', this.el).show();
    },
    
    saveEditImage: function(){
      $('div.edit-image', this.el).hide();
      
      var image = $('input.reward-image', this.el).val();
      
      this.model.set('image', image).trigger('change');
      
      
      this.options.vent.trigger('showAddRewardModal', this.model);
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
      
      console.log('set redeem:', redeem);
      
      this.model.set('company_id', parseInt(window.Company.companyId, 10));
      this.model.set('redeem', redeem).trigger('change');
      
      
      $('div.edit-redeem', this.el).show();
      $('div.edit-redeem-field', this.el).hide();
      
      this.options.vent.trigger('showAddRewardModal', this.model);
    },
    
    createReward: function(){
      
      console.log('create reward!');
      this.model.set('company_id', parseInt(window.Company.companyId, 10));

      this.options.rewardsCollection.create(this.model, {
        success: function() {
          //Refresh
          // window.location = window.Company.BASE_URL + 'r/company/' + window.Company.companyId +'/reward';
        }
      });
      
      this.$el.modal('hide');
    }
    
  });
  return EditModalView;
});
