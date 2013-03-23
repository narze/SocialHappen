define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/LikeEditTemplate.html'
], function($, _, Backbone, likeEditTemplate){
  var LikeFormView = Backbone.View.extend({

    likeEditTemplate: _.template(likeEditTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      var data = this.options.action;

      $(this.el).html(this.likeEditTemplate(data));

      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
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
      this.options.action.url = $('input.url', this.el).val();
      this.options.action.facebook_id = $('input.facebook_id', this.el).val();

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
  return LikeFormView;
});