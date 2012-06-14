define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/QRAddTemplate.html'
], function($, _, Backbone, QRTemplate){
  var QRAddView = Backbone.View.extend({
    QRTemplate: _.template(QRTemplate),
    tagName: 'li',
    
    events: {
      'click button.edit': 'showEdit',
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.QRTemplate(this.options.action));
      
      return this;
    },
    
    showEdit: function(){
      $('div.edit', this.el).toggle();
    },
    
    saveEdit: function(e){
      e.preventDefault();
      $('div.edit', this.el).hide();
      
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
    }
    
    
  });
  return QRAddView;
});
