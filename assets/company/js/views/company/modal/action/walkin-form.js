define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/WalkinEditTemplate.html'
], function($, _, Backbone, walkinEditTemplate){
  var WalkinFormView = Backbone.View.extend({

    walkinEditTemplate: _.template(walkinEditTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.generate-sonar-data': 'generateSonarData',
      'click button.cancel': 'cancelEdit'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      var data = this.options.action;
      data.sonar_code = this.model.get('sonar_frequency');
      $(this.el).html(this.walkinEditTemplate(data));
      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
    },

    generateSonarData: function() {
      var self = this
      $.ajax({
        type: 'GET',
        url: window.Company.BASE_URL + 'apiv3/get_sonar_box_data',
        dataType: 'JSON',
        success: function(res) {
          if(res.success) {
            $('.sonar-frequency', self.el).val(res.data)
          }
        }
      })
    },

    showEditName: function(){
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
      $('input.challenge-name', this.el).focus();
    },

    saveEdit: function(e){
      e.preventDefault();

      this.options.action.name = $('input.name', this.el).val();

      var criteria = this.model.get('criteria');
      this.model.set('criteria', criteria).trigger('change');
      this.model.set('sonar_frequency', $('.sonar-frequency', this.el).val()).trigger('change');
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
  return WalkinFormView;
});