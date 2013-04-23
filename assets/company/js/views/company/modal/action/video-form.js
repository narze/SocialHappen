define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/VideoEditTemplate.html'
], function($, _, Backbone, videoEditTemplate){
  var VideoFormView = Backbone.View.extend({

    videoEditTemplate: _.template(videoEditTemplate),

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

      $(this.el).html(this.videoEditTemplate(data));

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
      this.options.action.codes = _.map($('ul.codes li'), function(code){
        return $(code).attr('data-code');
      }) || [];

      var criteria = this.model.get('criteria');

      if(this.options.save){
        for(var i = criteria.length - 1; i >= 0; i--) {
          var actionItem = criteria[i];

          if(actionItem.action_data_id == this.options.action.action_data_id){
            console.log('found action to save', criteria[i]);
            criteria[i] = _.clone(this.options.action);
            console.log('criteria to be saved', criteria);
            break;
          }
        };
      }

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
  return VideoFormView;
});