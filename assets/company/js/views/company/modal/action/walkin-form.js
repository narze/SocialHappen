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
      'click button.cancel': 'cancelEdit'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      $(this.el).html(this.walkinEditTemplate(this.options.action));
      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
    },

    saveEdit: function(e){
      e.preventDefault();

      this.options.action.name = $('input.name', this.el).val();

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