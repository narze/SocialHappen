define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/modal/reward/add-reward.html',
  'jqueryui',
  'events',
  'sandbox'
], function($, _, Backbone, ChallengeModel, addTemplate,
   jqueryui, vent, sandbox){
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
      'change select.reward-redeem-method': 'setRewardRedeemMethod',
      'click .edit-redeem': 'showEditRedeem',
      'click .save-redeem': 'saveEditRedeem',
      'click button.create-reward': 'createReward',
      'click button.create-offer': 'createOffer',
      'click button.upload-image-submit': 'uploadImage'
    },

    initialize: function() {
      _.bindAll(this);
      vent.bind('showAddRewardModal', this.show);
    },

    render: function () {
      console.log('render modal');

      if(!this.model) {
        return;
      }

      var data = this.model.toJSON();
      console.log(data);
      $(this.el).html(this.addTemplate(data));

      //start/end timestamp
      var self = this
      $('.reward-start-date', self.el).datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('.reward-start-date', self.el).datetimepicker('getDate');

          var endDate = $('.reward-end-date', self.el).datetimepicker('getDate');

          if(endDate && date && date >= endDate){
            alert('Start date must come before end date');
            var startDate = self.model.get('start_date');
            if(startDate){
              startDate *= 1000;
              $('.reward-start-date', self.el).datetimepicker('setDate', (new Date(startDate)));
            }else{
              $('.reward-start-date', self.el).datetimepicker('setDate', null);
            }
            return;
          }

          self.model.set({
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
            var endDate = self.model.get('end_date');
            if(endDate){
              endDate *= 1000;
              $('.reward-end-date', self.el).datetimepicker('setDate', (new Date(endDate)));
            }else{
              $('.reward-end-date', self.el).datetimepicker('setDate', null);
            }
            return;
          }

          self.model.set({
            end_timestamp: Math.floor(date.getTime()/1000)
          });
        }
      });

      return this;
    },

    show: function(model) {
      this.model = model;

      console.log('show add modal:', this.model.toJSON());
      this.render();

      this.$el.modal('show');
    },

    showEditName: function() {
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
      $('input.reward-name', this.el).focus();
    },

    saveEditName: function() {

      var name = $('input.reward-name', this.el).val();

      this.model.set('name', name).trigger('change');


      $('h3.edit-name', this.$el).show();
      $('div.edit-name', this.$el).hide();

      vent.trigger('showAddRewardModal', this.model);
    },

    setRewardStatus: function() {
      var status = $('select.reward-status', this.el).val();
      console.log('set reward status to', status);

      this.model.set('status', status).trigger('change');
    },

    setRewardRedeemMethod: function() {
      var redeemMethod = $('select.reward-redeem-method', this.el).val();
      console.log('set reward redeem_method to', redeemMethod);

      this.model.set('redeem_method', redeemMethod).trigger('change');
    },

    showEditDescription: function() {
      $('div.edit-description', this.el).hide();
      $('div.edit-description-field', this.el).show();
    },

    saveEditDescription: function() {

      var description = $('textarea.reward-description', this.el).val();

      this.model.set('description', description).trigger('change');

      $('div.edit-description', this.el).show();
      $('div.edit-description-field', this.el).hide();

      vent.trigger('showAddRewardModal', this.model);
    },

    showEditImage: function() {
      $('div.edit-image', this.el).show();
    },

    saveEditImage: function() {
      $('div.edit-image', this.el).hide();

      var image = $('input.reward-image', this.el).val();

      this.model.set('image', image).trigger('change');


      vent.trigger('showAddRewardModal', this.model);
    },

    showEditRedeem: function() {
      $('div.edit-redeem', this.el).hide();
      $('div.edit-redeem-field', this.el).show();
    },

    saveEditRedeem: function() {

      var amount = $('input.reward-amount', this.el).val();
      var point = $('input.reward-point', this.el).val();
      var once = !_.isUndefined($('input.reward-once', this.el).attr('checked'));

      var redeem = this.model.get('redeem');

      redeem.amount = amount;
      redeem.amount_redeemed = 0;
      redeem.point = point;
      redeem.once = once;

      console.log('set redeem:', redeem);

      this.model.set('company_id', parseInt(window.Company.companyId, 10));
      this.model.set('redeem', redeem).trigger('change');


      $('div.edit-redeem', this.el).show();
      $('div.edit-redeem-field', this.el).hide();

      vent.trigger('showAddRewardModal', this.model);
    },

    createReward: function() {

      console.log('create reward!');
      this.model.set('company_id', parseInt(window.Company.companyId, 10));
      this.model.set('type', 'redeem');

      sandbox.collections.rewardsCollection.create(this.model, {
        success: function() {
          //Refresh
          // window.location = window.Company.BASE_URL + 'r/company/' + window.Company.companyId +'/reward';
        }
      });

      this.$el.modal('hide');
    },

    createOffer: function() {

      console.log('create offer!');
      this.model.set('company_id', parseInt(window.Company.companyId, 10));
      this.model.set('type', 'redeem');

      sandbox.collections.offersCollection.create(this.model, {
        success: function() {
          //Refresh
          // window.location = window.Company.BASE_URL + 'r/company/' + window.Company.companyId +'/offer';
        }
      });

      this.$el.modal('hide');
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

            vent.trigger('showAddRewardModal', self.model);
            return;
          }
          alert(resp.data);
        }
      })
    }

  });
  return EditModalView;
});
