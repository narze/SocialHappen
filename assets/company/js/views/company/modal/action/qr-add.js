define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/QRActionTemplate.html'
], function($, _, Backbone, QRActionTemplate){
  var QRAddView = Backbone.View.extend({
    QRActionTemplate: _.template(QRActionTemplate),
    tagName: 'li',
    formEl: null,
    events: {
      'click .edit-action': 'showEdit',
      'click .remove-action': 'remove'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.QRActionTemplate(this.options.action));
      return this;
    },
    
    showEdit: function(){
      this.formEl = $('.modal', this.el).appendTo('#action-modal').modal('show');
      $('button.save', this.formEl).click(this.saveEdit);
      $('button.cancel', this.formEl).click(this.cancelEdit);
    },
    
    saveEdit: function(e){
      e.preventDefault();
      console.log('model', this.model);
      this.options.action = {
        query: {
          action_id: 201
        },
        count: 1
      };
      this.options.action.name = $('input.name', this.formEl).val();
      this.options.action.action_data = {
        data: {},
        action_id: 201
      };
      this.options.action.action_data.data.todo_message = $('textarea.todo_message', this.formEl).val();
      this.options.action.action_data.data.done_message = $('textarea.done_message', this.formEl).val();
      
      var criteria = this.model.get('criteria');
      
      criteria.push(this.options.action);
      
      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    cancelEdit: function(e){
      e.preventDefault();
      $('div.edit', this.formEl).hide();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
      this.remove(e);
    },

    remove: function(e) {
      e.preventDefault();
      this.$el.remove();
      $('#action-modal').empty();
    },

    showForm: function() {
      this.formEl = $('.modal > *', this.el).not('.modal-header');
      $('#add-action-modal .add-action-form').html(this.formEl);
      $('button.save', this.formEl).click(this.saveEdit);
      $('button.cancel', this.formEl).click(this.cancelEdit);
    }
  });
  return QRAddView;
});
