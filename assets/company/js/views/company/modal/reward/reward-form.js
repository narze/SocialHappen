define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/reward/rewardEditTemplate.html'
], function($, _, Backbone, rewardFormTemplate){
  var QRFormView = Backbone.View.extend({
    rewardFormTemplate: _.template(rewardFormTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.rewardFormTemplate(this.options.reward));
      return this;
    },
    
    showEdit: function(){
      $(this.el).modal('show');
    },
    
    saveEdit: function(e){
      e.preventDefault();
      
      var reward = this.model.get('reward');
      console.log(reward);
      reward.name = $('input.reward-name', this.el).val();
      reward.image = $('input.reward-image', this.el).val();
      reward.value = $('input.reward-value', this.el).val();
      reward.status = $('select.reward-status', this.el).val();
      reward.description = $('textarea.reward-description', this.el).text();

      console.log(reward);
      this.model.set('reward', reward).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    cancelEdit: function(e){
      e.preventDefault();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return QRFormView;
});
