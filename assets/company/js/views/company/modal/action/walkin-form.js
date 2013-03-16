define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/WalkinEditTemplate.html',
  'chosen'
], function($, _, Backbone, walkinEditTemplate, chosen){
  var WalkinFormView = Backbone.View.extend({

    walkinEditTemplate: _.template(walkinEditTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.generate-sonar-data': 'generateSonarData',
      'click button.cancel': 'cancelEdit',
      'click button.new-code': 'onAddNewCode',
      'click a.remove-code': 'onRemoveCode'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      var data = this.options.action;

      data.deviceList = sandbox.collections.deviceCollection.toJSON();

      $(this.el).html(this.walkinEditTemplate(data));

      this.$('select.select-device').val(data.sonar_boxes);

      setTimeout(function(){
        $('.select-device.chzn-select').chosen();
      }, 100);

      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
    },

    onAddNewCode: function(e){
      var code = $.trim(this.$('input.code').val());
      this.$('input.code').val('');
      if(code){
        this.$('ul.codes').append($('<li data-code="'+code+'">'+code+' <a href="#" class="remove-code">remove</a></li>'));
      }
    },

    onRemoveCode: function(e){
      e.preventDefault();
      $(e.currentTarget).parent().remove();
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
      this.options.action.description = this.$('textarea.description').val();

      var devices = this.$('select.select-device').val();

      this.options.action.sonar_boxes = devices;

      this.options.action.codes = _.map(this.$('select.select-device option:selected'), function(code){
        return $(code).attr('data-data');
      }) || [];

      var criteria = this.model.get('criteria');
      this.model.set('criteria', criteria).trigger('change');
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