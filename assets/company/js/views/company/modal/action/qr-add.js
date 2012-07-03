define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/QRAddTemplate.html',
  'text!templates/company/modal/action/QRActionTemplate.html'
], function($, _, Backbone, QRTemplate, QRActionTemplate){
  var QRAddView = Backbone.View.extend({
    QRTemplate: _.template(QRTemplate),
    QRActionTemplate: _.template(QRActionTemplate),
    tagName: 'li',
    
    events: {
      'click .edit-action': 'showEdit',
      'click .remove-action': 'remove',
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.QRActionTemplate(this.options.action));
      $('#action-modal').html(this.QRTemplate(this.options.action));
      return this;
    },
    
    showEdit: function(){
      $('#action-modal').modal('show');
    },
    
    saveEdit: function(e){
      e.preventDefault();
      
      this.options.action = {
        query: {
          action_id: 201
        },
        count: 1
      };
      this.options.action.name = $('input.name', this.el).val();
      this.options.action.action_data = {
        data: {},
        action_id: 201
      };
      this.options.action.action_data.data.todo_message = $('textarea.todo_message', this.el).val();
      this.options.action.action_data.data.done_message = $('textarea.done_message', this.el).val();
      
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
      $('div.edit', this.el).hide();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
      this.remove();
    },

    remove: function(e) {
      e.preventDefault();
      this.$el.remove();
      $('#action-modal').empty();
    }
  });
  return QRAddView;
});
