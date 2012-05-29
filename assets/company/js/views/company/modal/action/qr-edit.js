define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/QREditTemplate.html'
], function($, _, Backbone, QRTemplate){
  var QREditView = Backbone.View.extend({
    QRTemplate: _.template(QRTemplate),
    tagName: 'li',
    
    events: {
      'click button.edit': 'showEdit',
      'click button.save': 'saveEdit',
      'click button.remove': 'removeAction'
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
      
      var dataId = this.options.action.action_data_id;
      
      this.options.action.name = $('textarea.name', this.el).val();
      this.options.action.action_data.data.todo_message = $('textarea.todo_message', this.el).val();
      this.options.action.action_data.data.done_message = $('textarea.done_message', this.el).val();
      
      var criteria = this.model.get('criteria');
      this.model.set('criteria', criteria).trigger('change');
      this.model.save();
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    removeAction: function(e){
      e.preventDefault();
      
      var dataId = this.options.action.action_data_id;
      var criteria = this.model.get('criteria');
      
      var target = $(e.currentTarget).parent().parent().parent().parent();
      var index = target.index();
            
      delete criteria[index];
      criteria = _.compact(criteria);
      
      this.model.set('criteria', criteria).trigger('change');
      this.model.save();
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
    
    
  });
  return QREditView;
});
