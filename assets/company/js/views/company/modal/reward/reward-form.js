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
      'click button.save-image': 'saveEditImage',
      'click button.upload-image-submit': 'uploadImage'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      console.log(this.options.reward_item);
      if(!this.options.reward_item.redeem_method) {
        this.options.reward_item.redeem_method = ''
      }
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
      this.options.reward_item.value = parseInt($('input.reward-value', this.el).val(), 10);
      this.options.reward_item.status = $('select.reward-status', this.el).val();
      this.options.reward_item.description = $('textarea.reward-description', this.el).val();
      this.options.reward_item.redeem_method = $('select.reward-redeem-method', this.el).val();
      this.options.reward_item.reward_machine_id = $('input.reward-machine-id', this.el).val();

      var reward_items = this.model.get('reward_items');
      this.model.set('reward_items', reward_items).trigger('change');

      console.log('save reward_items', reward_items, this.options.reward_item);

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

    uploadImage: function(e) {
      e.preventDefault();
      var self = this;
      $('form.upload-image', this.el).ajaxSubmit({
        beforeSubmit: function(a,f,o) {
          o.dataType = 'json';
        },
        success: function(resp) {
          if(resp.success) {
            var imageUrl = resp.data;

            // self.model.set('image', imageUrl).trigger('change');
            $('img.reward-image', self.el).attr('src', imageUrl);
            $('input.reward-image', self.el).val(imageUrl);
            // self.options.vent.trigger('showAddModal', self.model);

            // Save only image (because we removed old image already)
            self.options.reward_item.image = imageUrl;
            var reward_items = self.model.get('reward_items');
            self.model.set('reward_items', reward_items).trigger('change');
            if(self.options.save){
              self.model.save();
            }

            return;
          }
          alert(resp.data);
        }
      })
    },

    saveEditImage: function(){
      console.log('save image');
      var imageUrl = $('input.reward-image', this.el).val();
      $('img.reward-image', self.el).attr('src', imageUrl);
      // this.model.set('image', imageUrl).trigger('change');

      // this.options.vent.trigger('showAddModal', this.model);
    }
  });
  return QRFormView;
});
