define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/modal/branch/add-branch.html',
  'jqueryui',
  'events',
  'sandbox'
], function($, _, Backbone, ChallengeModel, addTemplate,
   jqueryui, vent, sandbox){
  var EditModalView = Backbone.View.extend({
    addTemplate: _.template(addTemplate),

    events: {
      'click h3.edit-title': 'showEditTitle',
      'click button.save-title': 'saveEditTitle',
      'click div.edit-address': 'showEditAddress',
      'click button.save-address': 'saveEditAddress',
      'click div.edit-hours': 'showEditHours',
      'click button.save-hours': 'saveEditHours',
      'click div.edit-telephone': 'showEditTelephone',
      'click button.save-telephone': 'saveEditTelephone',
      'click div.edit-location': 'showEditLocation',
      'click button.save-location': 'saveEditLocation',
      'click img.branch-photo, h6.edit-photo': 'showEditPhoto',
      'click button.save-photo': 'saveEditPhoto',
      'click button.create-branch': 'createBranch',
      'click button.upload-photo-submit': 'uploadPhoto'
    },

    initialize: function() {
      _.bindAll(this);
      vent.bind('showAddBranchModal', this.show);
    },

    render: function () {
      console.log('render modal');

      if(!this.model) {
        return;
      }

      var data = this.model.toJSON();
      console.log(data);
      $(this.el).html(this.addTemplate(data));

      return this;
    },

    show: function(model) {
      this.model = model;

      console.log('show add modal:', this.model.toJSON());
      this.render();

      this.$el.modal('show');
    },

    showEditTitle: function() {
      $('h3.edit-title', this.el).hide();
      $('div.edit-title', this.el).show();
      $('input.branch-title', this.el).focus();
    },

    saveEditTitle: function() {

      var title = $('input.branch-title', this.el).val();

      this.model.set('title', title).trigger('change');

      $('h3.edit-title', this.$el).html(title).show();
      $('div.edit-title', this.$el).hide();

      console.log('save title', title);

      // vent.trigger('showAddBranchModal', this.model);
    },

    showEditAddress: function() {
      $('div.edit-address', this.el).hide();
      $('div.edit-address-field', this.el).show();
    },

    saveEditAddress: function() {

      var address = $('textarea.branch-address', this.el).val();

      this.model.set('address', address).trigger('change');

      $('div.edit-address p', this.el).html(address);
      $('div.edit-address', this.el).show();
      $('div.edit-address-field', this.el).hide();

      // vent.trigger('showAddBranchModal', this.model);
    },

    showEditHours: function() {
      $('div.edit-hours', this.el).hide();
      $('div.edit-hours-field', this.el).show();
    },

    saveEditHours: function() {

      var hours = $('textarea.branch-hours', this.el).val();

      this.model.set('hours', hours).trigger('change');

      $('div.edit-hours p', this.el).html(hours);
      $('div.edit-hours', this.el).show();
      $('div.edit-hours-field', this.el).hide();

      // vent.trigger('showAddBranchModal', this.model);
    },

    showEditTelephone: function() {
      $('div.edit-telephone', this.el).hide();
      $('div.edit-telephone-field', this.el).show();
    },

    saveEditTelephone: function() {

      var telephone = $('input.branch-telephone', this.el).val();

      this.model.set('telephone', telephone).trigger('change');

      $('div.edit-telephone p', this.el).html(telephone);
      $('div.edit-telephone', this.el).show();
      $('div.edit-telephone-field', this.el).hide();

      // vent.trigger('showAddBranchModal', this.model);
    },

    showEditLocation: function() {
      $('div.edit-location', this.el).hide();
      $('div.edit-location-field', this.el).show();
    },

    saveEditLocation: function() {

      var lat = parseFloat($('input.lat', this.el).val()) || 0;
      var lng = parseFloat($('input.lng', this.el).val()) || 0;

      this.model.set('location', { '0': lat, '1':lng }).trigger('change');

      $('div.edit-location p span.lat', this.el).text(lat);
      $('div.edit-location p span.lng', this.el).text(lng);
      $('div.edit-location', this.el).show();
      $('div.edit-location-field', this.el).hide();

      // vent.trigger('showAddBranchModal', this.model);
    },


    showEditPhoto: function() {
      console.log('show edit photo');
      $('div.edit-photo', this.el).show();
    },

    saveEditPhoto: function() {
      $('div.edit-photo', this.el).hide();

      var photo = $('input.branch-photo', this.el).val();

      this.model.set('photo', photo).trigger('change');
      // $('img.branch-photo').attr('src', photo)

      vent.trigger('showAddBranchModal', this.model);
    },

    createBranch: function() {

      console.log('create branch!');
      this.model.set('company_id', parseInt(window.Company.companyId, 10));

      sandbox.collections.branchCollection.create(this.model, {
        success: function() {
          //Refresh
          // window.location = window.Company.BASE_URL + 'r/company/' + window.Company.companyId +'/branch';
        }
      });

      this.$el.modal('hide');
    },

    uploadPhoto: function(e) {
      e.preventDefault();
      var self = this;
      $('form.upload-photo', this.el).ajaxSubmit({
        beforeSubmit: function(a,f,o) {
          o.dataType = 'json';
        },
        success: function(resp) {
          if(resp.success) {
            var photoUrl = resp.data;

            // Save photo
            self.model.set('photo', photoUrl).trigger('change');

            vent.trigger('showAddBranchModal', self.model);
            return;
          }
          alert(resp.data);
        }
      })
    }

  });
  return EditModalView;
});
