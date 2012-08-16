define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/settings.html',
  'text!templates/company/settings-form.html',
  'events',
  'sandbox'
], function($, _, Backbone, companySettingsTemplate, companySettingsFormTemplate, vent, sandbox){
  var CompanySettingsView = Backbone.View.extend({

    events: {
      'click button.submit': 'submitForm'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.models.companyModel.bind('change', this.fillForm);
      sandbox.models.companyModel.bind('sync', this.synced);
      sandbox.models.companyModel.setCompanyId(window.Company.companyId);
      sandbox.models.companyModel.fetch();
    },

    render: function () {
      $(this.el).html(_.template(companySettingsTemplate)({}))
      return this;
    },

    fillForm: function() {
      $('.company-settings', this.el).html(_.template(companySettingsFormTemplate)({
        company: sandbox.models.companyModel.toJSON()
      }));
      console.log(sandbox.models.companyModel.toJSON());
    },

    submitForm: function(e) {
      e.preventDefault();
      sandbox.models.companyModel.set('company_image', $('form #company-image', this.el).val())
      sandbox.models.companyModel.set('company_name', $('form #company-name', this.el).val())
      sandbox.models.companyModel.set('company_address', $('form #company-address', this.el).val())
      sandbox.models.companyModel.set('company_detail', $('form #company-detail', this.el).val())
      sandbox.models.companyModel.set('company_email', $('form #company-email', this.el).val())
      sandbox.models.companyModel.set('company_telephone', $('form #company-phone', this.el).val())
      sandbox.models.companyModel.set('company_website', $('form #company-website', this.el).val())
      sandbox.models.companyModel.save();
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.models.companyModel.unbind();
    },

    synced: function() {
      $('.flash-message', this.el).html($('#updated-template').html());
    }
  });
  return CompanySettingsView;
});
