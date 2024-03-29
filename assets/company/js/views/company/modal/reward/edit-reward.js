define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/reward/edit-reward.html',
  'jqueryui',
  'events',
  'sandbox'
], function($, _, Backbone, editTemplate, jqueryui, vent, sandbox){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    events: {
      'click h3.edit-name': 'showEditName',
      'click button.save-name': 'saveEditName',
      'click div.edit-description': 'showEditDescription',
      'click button.save-description': 'saveEditDescription',
      'click div.edit-address': 'showEditAddress',
      'click button.save-address': 'saveEditAddress',
      'click div.edit-source': 'showEditSource',
      'click button.save-source': 'saveEditSource',
      'click img.reward-image, h6.edit-image': 'showEditImage',
      'click button.save-image': 'saveEditImage',
      'change select.reward-status': 'setRewardStatus',
      'change select.reward-redeem-method': 'setRewardRedeemMethod',
      'click .edit-redeem': 'showEditRedeem',
      'click .save-redeem': 'saveEditRedeem',
      'click button.upload-image-submit': 'uploadImage'
    },

    initialize: function(){
      _.bindAll(this);
      vent.bind('showEditRewardModal', this.show);
    },

    render: function () {
      console.log('render modal');
      if(!this.model){
        return;
      }

      var data = this.model.toJSON();
      $(this.el).html(this.editTemplate(data));

      //start/end timestamp
      var self = this
      $('.reward-start-date', self.el).datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('.reward-start-date', self.el).datetimepicker('getDate');

          var endDate = $('.reward-end-date', self.el).datetimepicker('getDate');

          if(endDate && date && date >= endDate){
            alert('Start date must come before end date');
            var startDate = self.model.get('start_timestamp');
            if(startDate){
              startDate *= 1000;
              $('.reward-start-date', self.el).datetimepicker('setDate', (new Date(startDate)));
            }else{
              $('.reward-start-date', self.el).datetimepicker('setDate', null);
            }
            return;
          }

          self.model.save({
            start_timestamp: Math.floor(date.getTime()/1000)
          });
        }
      });
      $('.reward-end-date', self.el).datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('.reward-end-date', self.el).datetimepicker('getDate');

          var startDate = $('.reward-start-date', self.el).datetimepicker('getDate');

          if(date && startDate && startDate >= date){
            alert('End date must come after start date');
            var endDate = self.model.get('end_timestamp');
            if(endDate){
              endDate *= 1000;
              $('.reward-end-date', self.el).datetimepicker('setDate', (new Date(endDate)));
            }else{
              $('.reward-end-date', self.el).datetimepicker('setDate', null);
            }
            return;
          }

          self.model.save({
            end_timestamp: Math.floor(date.getTime()/1000)
          });
        }
      });

      var startTimestamp = this.model.get('start_timestamp');
      if(startTimestamp){
        startTimestamp *= 1000;
        $('.reward-start-date', self.el).datetimepicker('setDate', (new Date(startTimestamp)));
      }

      var endTimestamp = this.model.get('end_timestamp');
      if(endTimestamp){
        endTimestamp *= 1000;
        $('.reward-end-date', self.el).datetimepicker('setDate', (new Date(endTimestamp)));
      }

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

      vent.trigger('showEditRewardModal', this.model);
    },

    setRewardStatus: function(){
      var status = $('select.reward-status', this.el).val();
      console.log('set reward status to', status);

      this.model.set('status', status).trigger('change');
      this.model.save();
    },

    setRewardRedeemMethod: function() {
      var redeemMethod = $('select.reward-redeem-method', this.el).val();
      console.log('set reward redeem_method to', redeemMethod);

      this.model.set('redeem_method', redeemMethod).trigger('change');
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

      vent.trigger('showEditRewardModal', this.model);
    },

    showEditAddress: function() {
      $('div.edit-address', this.el).hide();
      $('div.edit-address-field', this.el).show();
    },

    saveEditAddress: function() {

      var address = $('textarea.offer-address', this.el).val();

      this.model.set('address', address).trigger('change');
      this.model.save();

      // $('div.edit-address p', this.el).html(address);
      $('div.edit-address', this.el).show();
      $('div.edit-address-field', this.el).hide();

      vent.trigger('showEditRewardModal', this.model);
    },

    showEditSource: function() {
      $('div.edit-source', this.el).hide();
      $('div.edit-source-field', this.el).show();
    },

    saveEditSource: function() {

      var source = $('input.offer-source', this.el).val();

      this.model.set('source', source).trigger('change');
      this.model.save();

      // $('input.offer-source', this.el).val(source);
      $('div.edit-source', this.el).show();
      $('div.edit-source-field', this.el).hide();

      vent.trigger('showEditRewardModal', this.model);
    },

    showEditImage: function(){
      $('div.edit-image', this.el).show();
    },

    saveEditImage: function(){
      $('div.edit-image', this.el).hide();

      var image = $('input.reward-image', this.el).val();

      this.model.set('image', image).trigger('change');
      this.model.save();

      vent.trigger('showEditRewardModal', this.model);
    },

    showEditRedeem: function(){
      $('div.edit-redeem', this.el).hide();
      $('div.edit-redeem-field', this.el).show();
    },

    saveEditRedeem: function(){

      var amount = $('input.reward-amount', this.el).val();
      // var amount_remain = $('input.reward-amount-remain', this.el).val();
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

      vent.trigger('showEditRewardModal', this.model);
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

            // Save image
            self.model.set('image', imageUrl).trigger('change');

            //Save change
            self.model.save();

            vent.trigger('showEditRewardModal', self.model);
            return;
          }
          alert(resp.data);
        }
      })
    }
  });
  return EditModalView;
});
