define([
  'jquery',
  'underscore',
  'backbone',
  'views/company/modal/reward/reward-form',
  'text!templates/company/modal/reward/rewardTemplate.html'
], function($, _, Backbone, RewardFormView, RewardTemplate){
  var CheckinAddView = Backbone.View.extend({

    rewardTemplate: _.template(RewardTemplate),

    tagName: 'li',
    
    events: {
      'click .edit-reward': 'showEdit',
      'click .remove-reward': 'remove'
    },
    
    initialize: function(){
      _.bindAll(this);

      var reward = this.options.reward;
      this.model.set('reward', reward).trigger('change');
      
      if(this.options.save){
        this.model.save();
      }
    },
    
    render: function () {
      $(this.el).html(this.rewardTemplate(this.options.reward));
      return this;
    },
    
    showEdit: function(){
      var rewardFormView = new RewardFormView({
        model: this.model,
        reward: this.options.reward,
        vent: this.options.vent,
        triggerModal: this.options.triggerModal,
        save: this.options.save
      });
      $('#action-modal').html(rewardFormView.render().el);
      $('#action-modal').modal('show');
    },
    
    remove: function(e) {
      e.preventDefault();
      this.model.set('reward', {}).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return CheckinAddView;
});
