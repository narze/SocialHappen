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
      'click button.cancel': 'cancelEdit',
      'click h3.edit-name': 'showEditName',
      'click button.save-name': 'saveEditName',
      'click div.edit-description': 'showEditDescription',
      'click button.save-description': 'saveEditDescription',
      'click img.reward-image, h6.edit-image': 'showEditImage',
      'click button.save-image': 'saveEditImage'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      console.log(this.options.reward_item);
      $(this.el).html(this.rewardFormTemplate(this.options.reward_item));
      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
    },

    saveEdit: function(e){
      e.preventDefault();

      this.options.reward_item.name = $('input.reward-name', this.el).val();
      this.options.reward_item.image = $('input.reward-image', this.el).val();
      this.options.reward_item.value = $('input.reward-value', this.el).val();
      this.options.reward_item.status = $('select.reward-status', this.el).val();
      this.options.reward_item.description = $('textarea.reward-description', this.el).val();

      var reward_items = this.model.get('reward_items');
      this.model.set('reward_items', reward_items).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },

    cancelEdit: function(e){
      e.preventDefault();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },

    showEditName: function(){
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
    },

    saveEditName: function(){
      var name = $('input.reward-name', this.el).val();
      $('h3.edit-name', this.el).html(name).show();
      $('div.edit-name', this.el).hide();
    },

    showEditDescription: function(){
      $('div.edit-description', this.el).hide();
      $('div.edit-description-field', this.el).show();
    },

    saveEditDescription: function(){
      var description = $('textarea.reward-description', this.el).val();
      $('div.edit-description', this.el).show().find('p').text(description);
      $('div.edit-description-field', this.el).hide();
    },

    showEditImage: function(){
      $('div.edit-image', this.el).show();
    },

    saveEditImage: function(){
      $('div.edit-image', this.el).hide();
      var image = $('input.reward-image', this.el).val();
      $('img.reward-image').attr('src', image);
    }

  });
  return QRFormView;
});
